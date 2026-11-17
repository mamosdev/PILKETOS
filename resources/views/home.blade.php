<x-layouts.app title="Pemilihan">
    <div class="space-y-6">
        <h1 class="font-heading text-3xl font-extrabold">Pemilihan yang tersedia</h1>

        @forelse ($elections as $e)
        <a href="{{ route('portal', $e) }}"
            class="glass-card rounded-2xl p-5 flex items-center justify-between gap-4 hover:shadow-xl transition-all">
            <div>
                <p class="font-heading font-bold text-lg">{{ $e->title }}</p>
                <p class="text-sm text-slate-600">{{ $e->organization }}</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-white/90 border border-slate-200">{{
                $e->status->getLabel() }}</span>
        </a>
        @empty
        <p class="glass-card rounded-2xl p-6 text-slate-700">Belum ada pemilihan yang dibuka.</p>
        @endforelse
    </div>
</x-layouts.app>