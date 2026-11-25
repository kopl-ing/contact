@extends('kopling-admin::layouts.admin')

@section('content')
    <div class="max-w-3xl flex flex-col gap-6">
        <a href="{{ route('kopling-admin::admin/contact') }}" class="link link-hover text-sm">{{ __('kopling-contact::messages.admin.back') }}</a>

        <div>
            <h1 class="text-2xl font-bold">
                {{ $message->subjectLabel() }}
                @if ($message->context)
                    <span class="badge">{{ $message->context }}</span>
                @endif
            </h1>
            <p class="opacity-70">
                {{ $message->name ?: __('kopling-contact::messages.admin.no_name') }} &lt;{{ $message->email }}&gt;
                · {{ $message->created_at->isoFormat('LLL') }}
                @if ($message->person)
                    · {{ __('kopling-contact::messages.admin.signed_in_as', ['name' => $message->person->name]) }}
                @endif
            </p>
            @if ($message->handled_at)
                <span class="badge badge-success badge-sm mt-2">{{ __('kopling-contact::messages.admin.handled') }}</span>
            @endif
        </div>

        <div class="card bg-base-100 border border-base-300">
            <div class="card-body whitespace-pre-line">{{ $message->body }}</div>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="mailto:{{ $message->email }}?subject={{ rawurlencode(__('kopling-contact::messages.admin.reply_subject', ['subject' => $message->subjectLabel()])) }}"
               class="btn btn-primary">{{ __('kopling-contact::messages.admin.reply') }}</a>

            <form method="POST" action="{{ route('kopling-admin::admin/contact.handled', $message) }}" hx-boost="true">
                @csrf
                <button type="submit" class="btn">
                    {{ $message->handled_at ? __('kopling-contact::messages.admin.mark_open') : __('kopling-contact::messages.admin.mark_handled') }}
                </button>
            </form>

            <form method="POST" action="{{ route('kopling-admin::admin/contact.destroy', $message) }}"
                  hx-boost="true" hx-confirm="{{ __('kopling-contact::messages.admin.confirm_delete') }}">
                @csrf
                <button type="submit" class="btn btn-error btn-outline">{{ __('kopling-contact::messages.admin.delete') }}</button>
            </form>
        </div>
    </div>
@endsection
