<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

use App\Models\Election;
use App\Enums\ElectionStatus;
use App\Livewire\ElectionPortal;

use App\Http\Middleware\EnsureVoterCanVote;
use App\Livewire\VoterLogin;
use App\Livewire\VotingBooth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


Route::get('/cetak-token/{election}', function (Election $election) {
    abort_unless(auth()->check(), 403);

    $cards = Cache::get('token-cards:'.$election->id.':'.auth()->id());
    abort_if(blank($cards), 404, 'Daftar token sudah kedaluwarsa. Gunakan "Reset token" untuk membuat token baru.');

    return view('admin.token-cards', compact('election', 'cards'));
})->name('token-cards');

Route::get('/', function () {
    $elections = Election::where('status', '!=', ElectionStatus::Draft->value)
        ->latest('starts_at')->get();

    // Satu pemilihan saja: langsung ke portalnya
    if ($elections->count() === 1) {
        return redirect()->route('portal', $elections->first());
    }

    return view('home', compact('elections'));
})->name('home');

// Pemilihan portal
Route::get('/pemilihan/{election:slug}', ElectionPortal::class)->name('portal');

// Pemilih login & voting
Route::get('/pemilihan/{election:slug}/masuk', VoterLogin::class)->name('voter.login');

Route::get('/pemilihan/{election:slug}/bilik', VotingBooth::class)
    ->middleware(['auth:voter', EnsureVoterCanVote::class])
    ->name('booth');

Route::post('/pemilihan/{election:slug}/keluar', function (Request $request, Election $election) {
    Auth::guard('voter')->logout();
    $request->session()->regenerate();

    return redirect()->route('portal', $election);
})->name('voter.logout');