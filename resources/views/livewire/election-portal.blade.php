@php
$phase = $election->phase();
$pill = match ($phase) {
'open' => ['label' => 'Pemungutan Suara Berlangsung', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-600',
'ping' => 'bg-emerald-500', 'text' => 'text-emerald-950', 'prefix' => 'Sisa'],
'upcoming' => ['label' => 'Pemungutan Suara Belum Dibuka', 'border' => 'border-amber-200', 'dot' => 'bg-amber-600',
'ping' => 'bg-amber-500', 'text' => 'text-amber-950', 'prefix' => 'Dibuka dalam'],
default => ['label' => 'Pemungutan Suara Ditutup', 'border' => 'border-slate-300', 'dot' => 'bg-slate-500', 'ping' =>
'bg-slate-400', 'text' => 'text-slate-800', 'prefix' => null],
};
$target = match ($phase) {
'open' => $election->ends_at,
'upcoming' => $election->starts_at,
default => null,
};
@endphp

<div x-data="{ open: false, c: null, candidates: @js($this->modalData) }" @keydown.escape.window="open = false"
    class="space-y-12">

    {{-- HERO --}}
    <section class="glass-card rounded-3xl p-6 sm:p-10 lg:p-12 relative overflow-hidden" aria-label="Portal pemilih">
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-blue-500/15 rounded-full blur-3xl pointer-events-none">
        </div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-amber-400/15 rounded-full blur-3xl pointer-events-none">
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center relative z-10">
            <div class="lg:col-span-7 space-y-6">
                <div
                    class="inline-flex flex-wrap items-center gap-2.5 px-4 py-2 rounded-full bg-white/90 border {{ $pill['border'] }} shadow-sm">
                    <span class="relative flex h-2.5 w-2.5">
                        @if ($phase !== 'closed')
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $pill['ping'] }} opacity-75"></span>
                        @endif
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $pill['dot'] }}"></span>
                    </span>
                    <span class="text-xs sm:text-sm font-semibold {{ $pill['text'] }}">{{ $pill['label'] }}</span>
                    @if ($target)
                    <span class="text-slate-300">|</span>
                    <span class="text-xs sm:text-sm font-mono font-bold text-blue-900">
                        {{ $pill['prefix'] }}
                        <span x-data="countdown('{{ $target->toIso8601String() }}')" x-text="left"></span>
                    </span>
                    @endif
                </div>

                <h1
                    class="font-heading text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-950 tracking-tight leading-[1.15]">
                    {{ $election->title }}
                </h1>

                <p class="text-base sm:text-lg text-slate-700 leading-relaxed max-w-2xl">
                    {{ $election->description ?: 'Gunakan hak suaramu secara langsung, umum, bebas, dan rahasia. Satu
                    NISN dan token hanya berlaku untuk satu kali pilihan.' }}
                </p>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5 pt-1">
                    @if ($phase === 'open')
                    <a href="{{ route('voter.login', $election) }}"
                        class="min-h-[48px] px-7 py-3.5 rounded-2xl bg-blue-800 hover:bg-blue-900 text-white font-semibold text-base shadow-lg shadow-blue-900/20 transition-all flex items-center justify-center gap-2.5">
                        <span>Mulai Memilih (Login NISN)</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    @else
                    <span
                        class="min-h-[48px] px-7 py-3.5 rounded-2xl bg-slate-200 text-slate-600 font-semibold text-base flex items-center justify-center cursor-not-allowed">
                        {{ $phase === 'upcoming' ? 'Pemungutan suara belum dibuka' : 'Pemungutan suara telah ditutup' }}
                    </span>
                    @endif

                    <a href="#kandidat-section"
                        class="min-h-[48px] px-6 py-3.5 rounded-2xl bg-white/85 hover:bg-white text-slate-900 border border-slate-300 font-semibold text-base transition-all flex items-center justify-center">
                        Kenali {{ $this->candidates->count() }} Paslon
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5">
                <livewire:participation-stats :election="$election" />
            </div>
        </div>
    </section>

    {{-- ALUR 3 LANGKAH --}}
    <section class="space-y-4">
        <div>
            <h2 class="font-heading text-xl sm:text-2xl font-bold text-slate-950">Alur Pemungutan Suara Digital</h2>
            <p class="text-sm text-slate-600">Tiga langkah cepat dan aman untuk menyalurkan aspirasimu.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            @foreach ([
            ['Verifikasi NISN & Token', 'Masukkan NISN dan 6 karakter token unik yang dibagikan panitia.'],
            ['Pilih 1 Pasangan Calon', 'Tinjau visi, misi, dan program unggulan lalu pilih kandidatmu.'],
            ['Konfirmasi & e-Receipt', 'Sahkan pilihanmu di kotak konfirmasi dan simpan tanda bukti digital telah
            memilih.'],
            ] as [$stepTitle, $stepText])
            <div class="glass-card rounded-2xl p-5 sm:p-6 flex items-start gap-4">
                <div
                    class="w-11 h-11 rounded-2xl bg-blue-800 text-white font-heading font-extrabold text-lg flex items-center justify-center shrink-0 shadow-sm">
                    {{ $loop->iteration }}</div>
                <div>
                    <h3 class="font-heading font-bold text-slate-950 text-base">{{ $stepTitle }}</h3>
                    <p class="text-sm text-slate-700 mt-1 leading-relaxed">{{ $stepText }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- KANDIDAT --}}
    <section id="kandidat-section" class="space-y-6 scroll-mt-24">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-800">Daftar Kandidat Resmi</span>
                <h2 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-950 mt-1">Pasangan Calon</h2>
            </div>
            <p class="text-sm text-slate-600">Klik <strong>Visi &amp; Misi</strong> untuk membaca program kerja lengkap.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse ($this->candidates as $candidate)
            <article
                class="glass-card rounded-3xl p-5 sm:p-6 flex flex-col justify-between space-y-5 hover:shadow-xl transition-all duration-300">
                <div class="space-y-4">
                    <x-candidate-avatar :candidate="$candidate" />
                    <div>
                        @if ($candidate->subtitle)
                        <span class="text-xs font-semibold text-slate-600">{{ $candidate->subtitle }}</span>
                        @endif
                        <h3 class="font-heading text-xl font-extrabold text-slate-950 mt-0.5">{{
                            $candidate->display_name }}</h3>
                        @if ($candidate->tagline)
                        <p class="text-sm text-slate-700 mt-2 leading-relaxed">“{{ $candidate->tagline }}”</p>
                        @endif
                    </div>
                </div>

                <div class="pt-2 flex flex-col sm:flex-row gap-2.5">
                    <button type="button" @click="c = candidates[{{ $loop->index }}]; open = true"
                        class="flex-1 min-h-[44px] px-4 py-2.5 rounded-xl bg-white/90 hover:bg-white text-slate-900 border border-slate-300 font-semibold text-sm transition-all">
                        Visi &amp; Misi
                    </button>
                    @if ($phase === 'open')
                    <a href="{{ route('voter.login', $election) }}"
                        class="flex-1 min-h-[44px] px-4 py-2.5 rounded-xl bg-blue-800 hover:bg-blue-900 text-white font-semibold text-sm shadow-sm transition-all flex items-center justify-center">
                        Pilih Paslon {{ $candidate->number }}
                    </a>
                    @endif
                </div>
            </article>
            @empty
            <p class="md:col-span-3 glass-card rounded-2xl p-6 text-center text-slate-700">Daftar kandidat belum
                tersedia.</p>
            @endforelse
        </div>
    </section>

    {{-- MODAL VISI & MISI --}}
    <div x-show="open" x-cloak x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/50 backdrop-blur-sm" role="dialog"
        aria-modal="true" @click.self="open = false">
        <div
            class="glass-card-strong rounded-3xl max-w-2xl w-full max-h-[88vh] flex flex-col overflow-hidden shadow-2xl border border-white">
            <div class="p-6 border-b border-slate-200/80 flex items-start justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <span
                        class="w-12 h-12 rounded-2xl bg-blue-800 text-white font-heading font-extrabold text-xl flex items-center justify-center shrink-0"
                        x-text="c?.number"></span>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-800">Profil &amp; Gagasan
                            Kandidat</span>
                        <h3 class="font-heading text-xl font-extrabold text-slate-950" x-text="c?.name"></h3>
                    </div>
                </div>
                <button type="button" @click="open = false" aria-label="Tutup"
                    class="min-w-[44px] min-h-[44px] rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6 text-sm sm:text-base">
                <div class="p-4 rounded-2xl bg-blue-50/90 border border-blue-200/90">
                    <h4 class="font-heading font-bold text-blue-950 text-xs uppercase tracking-wider mb-1.5">Visi Utama
                    </h4>
                    <p class="text-slate-900 font-medium leading-relaxed" x-text="c?.vision"></p>
                </div>

                <div>
                    <h4 class="font-heading font-bold text-slate-950 text-xs uppercase tracking-wider mb-3">Misi &amp;
                        Langkah Strategis</h4>
                    <ul class="space-y-2.5 text-slate-700">
                        <template x-for="(m, idx) in (c?.missions ?? [])" :key="idx">
                            <li class="flex items-start gap-2.5">
                                <span
                                    class="w-6 h-6 rounded-full bg-blue-100 text-blue-900 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5"
                                    x-text="idx + 1"></span>
                                <span x-text="m"></span>
                            </li>
                        </template>
                    </ul>
                </div>

                <div>
                    <h4 class="font-heading font-bold text-slate-950 text-xs uppercase tracking-wider mb-3">Program
                        Unggulan</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <template x-for="(p, idx) in (c?.programs ?? [])" :key="idx">
                            <div class="p-3.5 rounded-2xl bg-white/90 border border-slate-200">
                                <p class="font-heading font-bold text-slate-950 text-sm" x-text="p.title"></p>
                                <p class="text-xs text-slate-600 mt-1" x-text="p.desc"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div
                class="p-4 sm:p-5 border-t border-slate-200/80 bg-slate-50/80 flex flex-col sm:flex-row justify-end gap-3">
                <button type="button" @click="open = false"
                    class="min-h-[44px] px-5 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-800 border border-slate-300 font-semibold text-sm transition-all">
                    Tutup
                </button>
                @if ($phase === 'open')
                <a href="{{ route('voter.login', $election) }}"
                    class="min-h-[44px] px-6 py-2.5 rounded-xl bg-blue-800 hover:bg-blue-900 text-white font-semibold text-sm shadow-md transition-all flex items-center justify-center">
                    Lanjut ke Bilik Suara
                </a>
                @endif
            </div>
        </div>
    </div>
</div>