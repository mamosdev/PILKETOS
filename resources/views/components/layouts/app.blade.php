<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="mesh-bg min-h-screen flex flex-col antialiased font-sans text-slate-900">
    <header class="glass-nav sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-linear-to-br from-blue-800 to-indigo-700 grid place-items-center text-white font-heading font-extrabold shadow-sm">
                    OS</div>
                <div>
                    <p class="font-heading font-extrabold text-lg leading-tight">{{ config('app.name') }}</p>
                    @isset($subtitle)
                    <p class="text-xs text-slate-600 hidden sm:block">{{ $subtitle }} • Sistem E-Voting</p>
                    @endisset
                </div>
            </a>
        </div>
    </header>

    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
        {{ $slot }}
    </main>

    <footer
        class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 text-xs text-slate-600 border-t border-slate-200/70">
        © {{ now()->year }} {{ $subtitle ?? config('app.name') }} • Sistem E-Voting
    </footer>

    @livewireScripts
</body>

</html>