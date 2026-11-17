<div class="max-w-md mx-auto">
  <div class="glass-card-strong rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl border border-white">
    <div>
      <span class="text-xs font-bold uppercase tracking-wider text-blue-800">Autentikasi Pemilih</span>
      <h1 class="font-heading text-2xl font-extrabold text-slate-950 mt-0.5">Masuk Bilik Suara</h1>
      <p class="text-sm text-slate-600 mt-1">{{ $election->title }}</p>
    </div>

    <form wire:submit="login" class="space-y-4" novalidate>
      <div>
        <label for="identifier" class="block text-sm font-semibold text-slate-900 mb-1.5">Nomor Induk Siswa Nasional (NISN)</label>
        <input type="text" id="identifier" wire:model="identifier" inputmode="numeric" autocomplete="off" required
               class="w-full min-h-[46px] px-4 py-2.5 rounded-xl bg-white border {{ $errors->has('identifier') ? 'border-red-400' : 'border-slate-300' }} text-slate-950 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-blue-700/40">
        @error('identifier')
          <p class="text-sm text-red-700 mt-1.5" role="alert">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="token" class="block text-sm font-semibold text-slate-900 mb-1.5">Token Pemilih (6 Karakter)</label>
        <input type="text" id="token" wire:model="token" maxlength="12" autocomplete="off" autocapitalize="characters" required
               class="w-full min-h-[46px] px-4 py-2.5 rounded-xl bg-white border {{ $errors->has('token') ? 'border-red-400' : 'border-slate-300' }} text-slate-950 font-mono uppercase tracking-widest text-sm focus:outline-none focus:ring-2 focus:ring-blue-700/40">
        @error('token')
          <p class="text-sm text-red-700 mt-1.5" role="alert">{{ $message }}</p>
        @enderror
        <p class="text-xs text-slate-600 mt-1.5">Token tertera di kartu yang dibagikan panitia.</p>
      </div>

      <button type="submit" wire:loading.attr="disabled" wire:target="login"
              class="w-full min-h-[48px] px-6 py-3 rounded-xl bg-blue-800 hover:bg-blue-900 disabled:opacity-60 text-white font-bold text-sm shadow-lg shadow-blue-900/20 transition-all">
        <span wire:loading.remove wire:target="login">Verifikasi &amp; Buka Bilik Suara</span>
        <span wire:loading wire:target="login">Memverifikasi…</span>
      </button>
    </form>

    <a href="{{ route('portal', $election) }}" class="block text-center text-sm font-semibold text-blue-800 hover:text-blue-900">
      ← Kembali ke portal
    </a>
  </div>
</div>