<?php

declare(strict_types=1);

namespace Kopling\Contact\Controllers;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\Rule;
use Kopling\Contact\Config;
use Kopling\Contact\Mail\NewMessage;
use Kopling\Contact\Message;
use Throwable;

class ContactController
{
    public const SESSION_KEY = 'kopling-contact.sent_at';

    public const MIN_SECONDS = 3;

    private const CONTEXT_PATTERN = '/^[\pL\pN_-]{1,100}$/u';

    public function show(Request $request): View
    {
        return view('kopling-contact::show', [
            'sent' => $this->coolingDown($request),
            'subjects' => Config::subjects(),
            'values' => $this->values($request),
        ]);
    }

    public function send(Request $request): View|RedirectResponse
    {
        if ($this->coolingDown($request) || $this->looksAutomated($request)) {
            return $this->sent($request);
        }

        $subjects = Config::subjects();

        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'subject' => ['required', 'string', Rule::in(array_keys($subjects))],
            'context' => ['nullable', 'string', 'regex:'.self::CONTEXT_PATTERN],
            'body' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'name.*' => __('kopling-contact::messages.form.name_invalid'),
            'email.*' => __('kopling-contact::messages.form.email_invalid'),
            'subject.*' => __('kopling-contact::messages.form.subject_missing'),
            'context.*' => __('kopling-contact::messages.form.subject_missing'),
            'body.required' => __('kopling-contact::messages.form.body_missing'),
            'body.min' => __('kopling-contact::messages.form.body_missing'),
            'body.*' => __('kopling-contact::messages.form.body_too_long'),
        ]);

        $validator->after(function ($validator) use ($request) {
            if (preg_match_all('~https?://|www\.~i', (string) $request->input('body')) > Config::maxLinks()) {
                $validator->errors()->add('body', __('kopling-contact::messages.form.too_many_links'));
            }
        });

        if ($validator->fails()) {
            return $this->invalid($request, $validator->errors());
        }

        $key = 'kopling-contact:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, Config::perHour())) {
            return $this->invalid($request, new MessageBag(['body' => __('kopling-contact::messages.form.throttled')]));
        }

        RateLimiter::hit($key, 3600);

        $data = $validator->validated();

        $message = Message::create([
            'name' => filled($data['name'] ?? null) ? $data['name'] : null,
            'email' => $data['email'],
            'subject' => $data['subject'],
            'context' => $data['context'] ?? null,
            'body' => $data['body'],
            'person_id' => $request->user()?->getKey(),
        ]);

        if ($address = Config::notifyAddress()) {
            try {
                Mail::to($address)->send(new NewMessage($message));
            } catch (Throwable $e) {
                report($e);
            }
        }

        return $this->sent($request);
    }

    private function sent(Request $request): View|RedirectResponse
    {
        $request->session()->put(self::SESSION_KEY, now()->getTimestamp());

        if ($request->hasHeader('HX-Request')) {
            return view('kopling-contact::partials.sent', ['focus' => true]);
        }

        return redirect()->route('kopling-contact::contact/show');
    }

    private function invalid(Request $request, MessageBag $errors): View|RedirectResponse
    {
        if ($request->hasHeader('HX-Request')) {
            return view('kopling-contact::partials.form', [
                'subjects' => Config::subjects(),
                'values' => $request->only(['name', 'email', 'subject', 'context', 'body', 'started']),
            ])->with('errors', (new ViewErrorBag)->put('default', $errors));
        }

        return redirect()->route('kopling-contact::contact/show')
            ->withErrors($errors)
            ->withInput($request->only(['name', 'email', 'subject', 'context', 'body', 'started']));
    }

    private function coolingDown(Request $request): bool
    {
        $sentAt = $request->session()->get(self::SESSION_KEY);

        return is_int($sentAt) && now()->getTimestamp() - $sentAt < Config::cooldownMinutes() * 60;
    }

    private function looksAutomated(Request $request): bool
    {
        if (filled($request->input('website'))) {
            return true;
        }

        try {
            $elapsed = now()->getTimestamp() - (int) decrypt((string) $request->input('started'));
        } catch (DecryptException) {
            return true;
        }

        return $elapsed < self::MIN_SECONDS || $elapsed > 86400;
    }

    /**
     * @return array<string, ?string>
     */
    private function values(Request $request): array
    {
        $subject = (string) $request->query('subject');
        $context = (string) $request->query('context');
        $person = $request->user();

        return [
            'name' => old('name', $person?->name),
            'email' => old('email', $person?->email),
            'subject' => old('subject', array_key_exists($subject, Config::subjects()) ? $subject : null),
            'context' => old('context', preg_match(self::CONTEXT_PATTERN, $context) ? $context : null),
            'body' => old('body'),
            'started' => old('started'),
        ];
    }
}
