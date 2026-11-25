@extends('kopling-admin::layouts.admin')

@section('content')
    <div class="max-w-5xl flex flex-col gap-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('kopling-contact::messages.admin.heading') }}</h1>
            <p class="opacity-70">
                {{ trans_choice('kopling-contact::messages.admin.open', $open, ['count' => $open]) }}
                {{ trans_choice('kopling-contact::messages.admin.retention', $retentionDays, ['days' => $retentionDays]) }}
            </p>
        </div>

        @if ($messages->isEmpty())
            <p class="opacity-60">{{ __('kopling-contact::messages.admin.empty') }}</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('kopling-contact::messages.admin.received') }}</th>
                        <th>{{ __('kopling-contact::messages.form.subject') }}</th>
                        <th>{{ __('kopling-contact::messages.admin.from') }}</th>
                        <th>{{ __('kopling-contact::messages.form.body') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($messages as $message)
                        <tr class="{{ $message->handled_at ? 'opacity-60' : 'font-semibold' }}">
                            <td class="whitespace-nowrap">{{ $message->created_at->diffForHumans() }}</td>
                            <td>
                                {{ $message->subjectLabel() }}
                                @if ($message->context)
                                    <span class="badge badge-sm">{{ $message->context }}</span>
                                @endif
                            </td>
                            <td>{{ $message->sender() }}</td>
                            <td class="font-normal">{{ \Illuminate\Support\Str::limit($message->body, 80) }}</td>
                            <td>
                                <a href="{{ route('kopling-admin::admin/contact.show', $message) }}" class="btn btn-sm">{{ __('kopling-contact::messages.admin.open_message') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $messages->links() }}
        @endif
    </div>
@endsection
