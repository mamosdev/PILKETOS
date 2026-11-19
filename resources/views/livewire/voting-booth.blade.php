<div x-data="{ selected: null, confirm: false, busy: false, labels: @js((object) $labels) }"
     @keydown.escape.window="confirm = false"
     class="space-y-6">

  @if ($receiptCode)

    {{-- E-RECEIPT --}}
    <div class="max-w-xl mx-auto glass-card-strong rounded-3xl p-6 sm:p-8 text-center space-y-6 border border-white shadow-2xl">
      <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-800 mx-auto flex items-center justify-center">
        <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
      </div>

      <div class="space-y-2">
        <h1 class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-950">Suara Anda Telah Tercatat</h1>
        <p class="text-sm sm:text-base text-slate-700 leading-relaxed">
          Terima kasih telah menggunakan hak pilihmu. Pilihanmu bersifat rahasia dan tidak tercantum di bukti ini.
        </p>
      </div>

      <dl class="text-sm rounded-2xl bg-slate-50/90 border border-slate-200 divide-y divide-slate-200 text-left">
        <div class="flex items-center justify-between gap-4 px-4 py-3">
          <dt class="text-slate-600">Pemilih</dt>
          <dd class="font-semibold text-slate-950 text-right">{{ $voterName }}</dd>
        </div>
        <div class="flex items-center justify-between gap-4 px-4 py-3">
          <dt class="text-slate-600">Kode e-Receipt</dt>
          <dd class="font-mono font-bold text-slate-950 tracking-wider">{{ $receiptCode }}</dd>
        </div>
        <div class="flex items-center justify-between gap-4 px-4 py-3">
          <dt class="text-slate-600">Waktu</dt>
          <dd class="font-semibold text-slate-950 text-right">{{ $votedAt }}</dd>
        </div>
      </dl>

      <p class="text-xs text-slate-600 leading-relaxed">
        Catat atau tangkap layar kode ini sekarang. Halaman ini tidak bisa dibuka lagi setelah kamu menutupnya.
        Kode ini tidak memuat pilihanmu.
      </p>

      <a href="{{ route('portal', $election) }}"
         class="block min-h-[48px] px-6 py-3 rounded-xl bg-blue-800 hover:bg-blue-900 text-white font-bold text-sm shadow-lg shadow-blue-900/20 transition-all">
        Kembali ke Portal
      </a>
    </div>

  @else

    @php($voter = $this->participation->voter)

    {{-- STRIP PEMILIH --}}
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

    @error('vote')
      <div class="rounded-2xl bg-red-50 border border-red-300 px-5 py-3 text-sm font-medium text-red-900" role="alert">
        {{ $message }}
      </div>
    @enderror

    {{-- SURAT SUARA --}}
    <section class="space-y-6 pb-32" aria-labelledby="judul-surat-suara">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-blue-800">Surat Suara Digital</span>
        <h1 id="judul-surat-suara" class="font-heading text-2xl sm:text-3xl font-extrabold text-slate-950 mt-1">Pilih Satu Pasangan Calon</h1>
        <p class="text-sm text-slate-700 mt-1">Baca visi dan misi, lalu pilih satu kandidat. Pilihan tidak dapat diubah setelah dikirim.</p>
      </div>

      <div role="radiogroup" aria-label="Pilih salah satu pasangan calon" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($this->candidates as $c)
          <article class="glass-card rounded-3xl p-5 sm:p-6 flex flex-col gap-4 transition-all duration-200"
                   :class="selected === {{ $c->id }} ? 'ring-4 ring-blue-700/70 -translate-y-0.5 shadow-xl' : 'hover:shadow-xl'">
            <x-candidate-avatar :candidate="$c" />

            <div>
              @if ($c->subtitle)
                <span class="text-xs font-semibold text-slate-600">{{ $c->subtitle }}</span>
              @endif
              <h2 class="font-heading text-xl font-extrabold text-slate-950 mt-0.5">{{ $c->display_name }}</h2>
              @if ($c->tagline)
                <p class="text-sm text-slate-700 mt-2 leading-relaxed">“{{ $c->tagline }}”</p>
              @endif
            </div>

            <details class="group rounded-xl bg-white/70 border border-slate-200 px-4 py-2.5 text-sm">
              <summary class="cursor-pointer font-semibold text-blue-900 select-none">Baca visi &amp; misi</summary>
              <div class="pt-3 space-y-3 text-slate-700">
                @if ($c->vision)
                  <p class="font-medium text-slate-900">{{ $c->vision }}</p>
                @endif
                @if (! empty($c->missions))
                  <ol class="list-decimal pl-5 space-y-1.5">
                    @foreach ($c->missions as $mission)
                      <li>{{ is_array($mission) ? reset($mission) : $mission }}</li>
                    @endforeach
                  </ol>
                @endif
              </div>
            </details>

            <button type="button" role="radio"
                    :aria-checked="(selected === {{ $c->id }}).toString()"
                    @click="selected = {{ $c->id }}"
                    class="mt-auto min-h-[48px] px-4 py-2.5 rounded-xl font-bold text-sm transition-all"
                    :class="selected === {{ $c->id }} ? 'bg-blue-800 text-white shadow-md shadow-blue-900/20' : 'bg-white/90 hover:bg-white text-slate-900 border border-slate-300'">
              <span x-text="selected === {{ $c->id }} ? '✓ Terpilih' : @js('Pilih Paslon '.$c->number)"></span>
            </button>
          </article>
        @endforeach

        @if ($election->allow_blank_vote)
          <article class="glass-card rounded-3xl p-5 sm:p-6 flex flex-col justify-between gap-4 border-2 border-dashed border-slate-300 transition-all"
                   :class="selected === 'blank' ? 'ring-4 ring-blue-700/70' : ''">
            <div>
              <h2 class="font-heading text-xl font-extrabold text-slate-950">Suara Kosong</h2>
              <p class="text-sm text-slate-700 mt-2 leading-relaxed">Gunakan hak pilih tanpa memilih satu pun pasangan calon.</p>
            </div>
            <button type="button" role="radio"
                    :aria-checked="(selected === 'blank').toString()"
                    @click="selected = 'blank'"
                    class="min-h-[48px] px-4 py-2.5 rounded-xl font-bold text-sm transition-all"
                    :class="selected === 'blank' ? 'bg-blue-800 text-white' : 'bg-white/90 hover:bg-white text-slate-900 border border-slate-300'">
              <span x-text="selected === 'blank' ? '✓ Terpilih' : 'Pilih Suara Kosong'"></span>
            </button>
          </article>
        @endif
      </div>
    </section>

    {{-- BAR PILIHAN SEMENTARA --}}
    <div x-show="selected !== null" x-cloak x-transition
         class="fixed bottom-4 inset-x-4 sm:left-1/2 sm:right-auto sm:-translate-x-1/2 sm:w-full sm:max-w-3xl z-40 glass-card-strong rounded-2xl p-4 shadow-2xl border border-white"
         role="region" aria-live="polite">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <p class="text-xs font-semibold uppercase tracking-wider text-blue-900">Pilihan Sementara Anda</p>
          <p class="font-heading font-bold text-slate-950 text-sm sm:text-base" x-text="labels[selected]"></p>
        </div>

        <div class="flex items-center gap-2.5">
          <button type="button" @click="selected = null"
                  class="min-h-[44px] px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold transition-all">
            Batal
          </button>
          <button type="button" @click="confirm = true"
                  class="flex-1 sm:flex-initial min-h-[44px] px-6 py-2.5 rounded-xl bg-blue-800 hover:bg-blue-900 text-white text-sm font-bold shadow-md shadow-blue-900/20 transition-all">
            Kirim Suara
          </button>
        </div>
      </div>
    </div>

    {{-- DIALOG KONFIRMASI --}}
    <div x-show="confirm" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/55 backdrop-blur-sm"
         role="dialog" aria-modal="true" aria-labelledby="judul-konfirmasi"
         @click.self="if (!busy) confirm = false">
      <div class="glass-card-strong rounded-3xl max-w-md w-full p-6 sm:p-7 space-y-5 shadow-2xl border border-white text-center">
        <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-900 mx-auto flex items-center justify-center">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
        </div>

        <div class="space-y-2">
          <h3 id="judul-konfirmasi" class="font-heading text-xl font-extrabold text-slate-950">Konfirmasi Pilihan Anda</h3>
          <p class="text-sm text-slate-700 leading-relaxed">
            Anda akan memilih <strong class="text-blue-900" x-text="labels[selected]"></strong>.
            Pilihan tidak dapat diubah setelah dikirim.
          </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
          <button type="button" @click="confirm = false" :disabled="busy"
                  class="flex-1 min-h-[46px] px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 disabled:opacity-60 text-slate-900 font-semibold text-sm transition-all">
            Periksa Kembali
          </button>
          <button type="button" :disabled="busy"
                  @click="busy = true; $wire.submit(selected).finally(() => { busy = false; confirm = false })"
                  class="flex-1 min-h-[46px] px-4 py-2.5 rounded-xl bg-blue-800 hover:bg-blue-900 disabled:opacity-60 text-white font-bold text-sm shadow-md transition-all">
            <span x-show="!busy">Ya, Sahkan Pilihan</span>
            <span x-show="busy" x-cloak>Mengirim…</span>
          </button>
        </div>
      </div>
    </div>

  @endif
</div>