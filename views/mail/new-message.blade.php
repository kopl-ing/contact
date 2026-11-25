{{ __('kopling-contact::messages.mail.intro', ['subject' => $subject]) }}
@if ($context)
{{ __('kopling-contact::messages.mail.context', ['context' => $context]) }}
@endif

{{ __('kopling-contact::messages.mail.read') }}
{{ $url }}
