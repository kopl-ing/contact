@extends('kopling-contact::layouts.contact')

@section('title', __('kopling-contact::messages.title'))
@section('description', __('kopling-contact::messages.intro'))

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-10 lg:py-16">
        <x-k::portal.slot name="kopling-contact::contact.intro" />

        <div class="grid gap-10 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <h1 class="text-3xl font-bold">{{ __('kopling-contact::messages.heading') }}</h1>
                <p class="mt-3 text-lg leading-relaxed">{{ __('kopling-contact::messages.intro') }}</p>

                <div class="mt-8">
                    @if ($sent)
                        @include('kopling-contact::partials.sent')
                    @else
                        @include('kopling-contact::partials.form')
                    @endif
                </div>
            </div>

            <aside>
                <x-k::portal.slot name="kopling-contact::contact.aside" />
            </aside>
        </div>
    </div>
@endsection
