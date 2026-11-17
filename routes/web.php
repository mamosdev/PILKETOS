<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Models\Election;
use App\Enums\ElectionStatus;
use App\Livewire\ElectionPortal;


// Route::get('/', function () {
//     return view('home');
// });
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

Route::get('/pemilihan/{election:slug}', ElectionPortal::class)->name('portal');

// Sementara: halaman ini dibuat sungguhan di Segmen 5
Route::get('/pemilihan/{election:slug}/masuk', fn (Election $election) => 'Halaman login pemilih (Segmen 5)')
    ->name('voter.login');