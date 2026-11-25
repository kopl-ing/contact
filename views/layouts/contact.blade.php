<x-k::portal.layout>
    <div class="flex flex-col min-h-screen">
        <header class="navbar bg-base-100 border-b border-base-300 px-4">
            <div class="flex-1">
                <a href="{{ url('/') }}" class="text-lg font-bold">{{ \Kopling\Core\Settings\Settings::get('kopling-core::community-name', config('app.name')) }}</a>
            </div>
            <nav class="flex-none flex items-center gap-4">
                <x-k::portal.slot name="kopling-contact::contact.topbar" />
            </nav>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>
    </div>
</x-k::portal.layout>
