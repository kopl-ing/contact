<div id="kopling-contact-form" role="status" @if ($focus ?? false) tabindex="-1" autofocus @endif
     class="alert alert-success alert-soft items-start scroll-mt-24 outline-none">
    <x-k::icon name="kopling-contact::contact" class="size-5 mt-0.5" />
    <div>
        <h2 class="font-bold">{{ __('kopling-contact::messages.sent.heading') }}</h2>
        <p>{{ __('kopling-contact::messages.sent.body') }}</p>
    </div>
</div>
