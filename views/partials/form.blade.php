<form id="kopling-contact-form" method="POST" action="{{ route('kopling-contact::contact/send') }}"
      hx-post="{{ route('kopling-contact::contact/send') }}" hx-target="this" hx-swap="outerHTML"
      class="flex flex-col gap-4">
    @csrf
    <input type="hidden" name="started" value="{{ $values['started'] ?? encrypt(now()->getTimestamp()) }}">

    <div class="sr-only" aria-hidden="true">
        <label>{{ __('kopling-contact::messages.form.website') }}
            <input type="text" name="website" value="" tabindex="-1" autocomplete="off">
        </label>
    </div>

    @if (filled($values['context'] ?? null))
        <input type="hidden" name="context" value="{{ $values['context'] }}">
    @endif

    <fieldset class="fieldset">
        <legend class="fieldset-legend">{{ __('kopling-contact::messages.form.subject') }}</legend>
        <select name="subject" class="select w-full {{ $errors->has('subject') ? 'select-error' : '' }}" required>
            <option value="" disabled @selected(blank($values['subject'] ?? null))>{{ __('kopling-contact::messages.form.subject_placeholder') }}</option>
            @foreach ($subjects as $key => $label)
                <option value="{{ $key }}" @selected(($values['subject'] ?? null) === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @if (filled($values['context'] ?? null))
            <p class="label"><span class="badge badge-sm">{{ $values['context'] }}</span></p>
        @endif
        @error('subject')
            <p class="label text-error">{{ $message }}</p>
        @enderror
    </fieldset>

    <fieldset class="fieldset">
        <legend class="fieldset-legend">{{ __('kopling-contact::messages.form.name') }} <span class="font-normal opacity-60">{{ __('kopling-contact::messages.form.optional') }}</span></legend>
        <input type="text" name="name" value="{{ $values['name'] ?? '' }}" maxlength="100" autocomplete="name"
               class="input w-full {{ $errors->has('name') ? 'input-error' : '' }}">
        @error('name')
            <p class="label text-error">{{ $message }}</p>
        @enderror
    </fieldset>

    <fieldset class="fieldset">
        <legend class="fieldset-legend">{{ __('kopling-contact::messages.form.email') }}</legend>
        <input type="email" name="email" value="{{ $values['email'] ?? '' }}" required autocomplete="email"
               class="input w-full {{ $errors->has('email') ? 'input-error' : '' }}">
        <p class="label">{{ __('kopling-contact::messages.form.email_hint') }}</p>
        @error('email')
            <p class="label text-error">{{ $message }}</p>
        @enderror
    </fieldset>

    <fieldset class="fieldset">
        <legend class="fieldset-legend">{{ __('kopling-contact::messages.form.body') }}</legend>
        <textarea name="body" rows="7" required minlength="10" maxlength="5000"
                  class="textarea w-full {{ $errors->has('body') ? 'textarea-error' : '' }}">{{ $values['body'] ?? '' }}</textarea>
        @error('body')
            <p class="label text-error">{{ $message }}</p>
        @enderror
    </fieldset>

    <div>
        <button type="submit" class="btn btn-primary">{{ __('kopling-contact::messages.form.send') }}</button>
    </div>
</form>
