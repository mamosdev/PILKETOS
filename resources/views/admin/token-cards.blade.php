<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Token — {{ $election->title }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            .card {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body class="bg-slate-100 p-6 font-sans">
    <div class="no-print max-w-5xl mx-auto mb-6 flex items-center justify-between gap-4">
        <p class="text-sm text-slate-700">
            {{ count($cards) }} kartu. Data token hilang otomatis dari server dalam 30 menit.
        </p>
        <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-blue-800 text-white font-semibold">
            Cetak
        </button>
    </div>

    <div class="max-w-5xl mx-auto grid grid-cols-2 gap-3">
        @foreach ($cards as $c)
        <div class="card border border-dashed border-slate-400 rounded-xl p-4 bg-white">
            <p class="text-xs uppercase tracking-wider text-slate-500">{{ $election->title }}</p>
            <p class="font-heading font-bold text-lg mt-1">{{ $c['name'] }}</p>
            <p class="text-sm text-slate-600">{{ $c['group'] }} · NISN {{ $c['identifier'] }}</p>
            <p class="mt-3 text-xs text-slate-500">Token pemilih</p>
            <p class="font-mono text-3xl font-bold tracking-[0.3em]">{{ $c['token'] }}</p>
        </div>
        @endforeach
    </div>
</body>

</html>