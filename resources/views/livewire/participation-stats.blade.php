<div wire:poll.15s class="glass-card-strong rounded-2xl p-6 sm:p-7 space-y-5 border border-white">
    <div class="flex items-center justify-between">
        <span
            class="text-xs font-bold uppercase tracking-wider text-blue-900 bg-blue-100/90 px-3 py-1 rounded-full">Partisipasi
            Real-Time</span>
        <span class="text-xs font-medium text-slate-600">DPT: {{ number_format($total, 0, ',', '.') }} pemilih</span>
    </div>

    <div>
        <div class="flex items-baseline justify-between mb-2">
            <span class="font-heading text-4xl sm:text-5xl font-extrabold text-slate-950">{{ number_format($pct, 1, ',',
                '.') }}%</span>
            <span class="text-sm font-semibold text-emerald-800 bg-emerald-100/90 px-2.5 py-0.5 rounded-lg">
                {{ number_format($voted, 0, ',', '.') }} Suara Masuk
            </span>
        </div>
        <div class="w-full h-3.5 bg-slate-200/90 rounded-full overflow-hidden p-0.5" role="progressbar"
            aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"
            aria-label="Persentase partisipasi pemilih">
            <div class="h-full bg-linear-to-r from-blue-800 to-indigo-600 rounded-full transition-all duration-500"
                style="width: {{ $pct }}%"></div>
        </div>
    </div>

    @if (! empty($cohorts))
    <div class="grid grid-cols-3 gap-2.5 pt-2 border-t border-slate-200/80 text-center">
        @foreach ($cohorts as $label => $value)
        <div class="p-2.5 rounded-xl bg-slate-50/90">
            <p class="text-xs text-slate-600 font-medium">Kelas {{ $label }}</p>
            <p class="font-heading font-bold text-slate-900 text-base mt-0.5">{{ number_format($value, 1, ',', '.') }}%
            </p>
        </div>
        @endforeach
    </div>
    @endif

    <p class="text-xs text-slate-600">Identitas pemilih dipisahkan dari data pilihan.</p>
</div>