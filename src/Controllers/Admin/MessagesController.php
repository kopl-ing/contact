<?php

declare(strict_types=1);

namespace Kopling\Contact\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Kopling\Contact\Config;
use Kopling\Contact\Message;

class MessagesController
{
    public function index(): View
    {
        return view('kopling-contact::admin.index', [
            'messages' => Message::query()->latest()->paginate(50),
            'open' => Message::query()->whereNull('handled_at')->count(),
            'retentionDays' => Config::retentionDays(),
        ]);
    }

    public function show(Message $message): View
    {
        return view('kopling-contact::admin.show', [
            'message' => $message->load('person'),
        ]);
    }

    public function handled(Message $message): RedirectResponse
    {
        $message->update(['handled_at' => $message->handled_at ? null : now()]);

        return redirect()->route('kopling-admin::admin/contact.show', $message);
    }

    public function destroy(Message $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('kopling-admin::admin/contact');
    }
}
