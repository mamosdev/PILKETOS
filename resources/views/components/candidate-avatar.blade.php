@props(['candidate'])

<div
    class="relative w-full h-44 rounded-2xl overflow-hidden shadow-inner {{ $candidate->photo_url ? 'bg-slate-200' : 'bg-linear-to-br '.$candidate->accent_gradient }}">
    @if ($candidate->photo_url)
    <img src="{{ $candidate->photo_url }}" alt="Foto paslon {{ $candidate->number }}" class="w-full h-full object-cover"
        loading="lazy">
    @else
    <div class="absolute inset-0 opacity-20">
        <svg class="w-full h-full" viewBox="0 0 200 120" fill="none" aria-hidden="true">
            <circle cx="170" cy="20" r="65" fill="white" />
            <circle cx="30" cy="110" r="55" fill="white" />
        </svg>
    </div>
    <div class="absolute inset-0 p-4 flex items-end justify-center">
        <div class="relative z-10 flex items-end gap-2 translate-y-2">
            <svg class="w-24 h-24 text-white/95" viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                <circle cx="32" cy="20" r="11" />
                <path d="M12 56c0-11 9-18 20-18s20 7 20 18H12z" />
            </svg>
            <svg class="w-20 h-20 text-white/80 -ml-4" viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                <circle cx="32" cy="22" r="10" />
                <path d="M14 56c0-10 8-16 18-16s18 6 18 16H14z" />
            </svg>
        </div>
    </div>
    @endif

    <span
        class="absolute top-3.5 left-3.5 px-3.5 py-1.5 rounded-xl bg-white/95 text-slate-950 font-heading font-extrabold text-xl shadow-md">
        {{ $candidate->number }}
    </span>
</div>