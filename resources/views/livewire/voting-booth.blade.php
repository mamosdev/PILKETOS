@php($voter = $this->participation->voter)

<div class="space-y-6">
  <div class="glass-card rounded-2xl p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3.5">
      <div class="w-12 h-12 rounded-2xl bg-blue-800 text-white font-heading font-extrabold text-lg flex items-center justify-center shrink-0">
        {{ Str::upper(Str::substr($voter->name, 0, 1)) }}
      </div>
      <div>
        <p class="font-heading font-bold text-slate-950 leading-tight">{{ $voter->name }}</p>
        <p class="text-xs text-slate-600">{{ $voter->group_name }} • NISN {{ Str::mask($voter->identifier, '•', 4, -2) }}</p>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2.5">
      <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-950 border border-amber-300">Belum memilih</span>

      @if ($election->ends_at)
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50/90 border border-emerald-200 text-emerald-900 text-xs font-semibold">
          Sisa
          <span class="font-mono" x-data="countdown('{{ $election->ends_at->toIso8601String() }}')" x-text="left"></span>
        </span>
      @endif

      <form method="POST" action="{{ route('voter.logout', $election) }}">
        @csrf
        <button type="submit" class="min-h-[36px] px-3 rounded-xl bg-white/90 hover:bg-white border border-slate-300 text-slate-800 text-xs font-semibold">
          Keluar
        </button>
      </form>
    </div>
  </div>

  <div class="glass-card rounded-3xl p-8 sm:p-12 text-center space-y-2">
    <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-950">Bilik Suara</h1>
    <p class="text-slate-700">Autentikasi berhasil. Surat suara digital kita buat di Segmen 6.</p>
  </div>
</div>