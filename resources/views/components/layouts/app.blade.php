<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="mesh-bg min-h-screen flex flex-col antialiased font-sans text-slate-900">
    <header class="glass-nav sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-800 to-indigo-700 grid place-items-center text-white font-heading font-extrabold">
                OS</div>
            <span class="font-heading font-extrabold text-lg">{{ config('app.name') }}</span>
        </div>
    </header>

    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>
</body>

</html>