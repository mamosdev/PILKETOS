<?php

use Illuminate\Support\Facades\Route;
use App\Models\Election;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    return view('home');
});
Route::get('/cetak-token/{election}', function (Election $election) {
    abort_unless(auth()->check(), 403);

    $cards = Cache::get('token-cards:'.$election->id.':'.auth()->id());
    abort_if(blank($cards), 404, 'Daftar token sudah kedaluwarsa. Gunakan "Reset token" untuk membuat token baru.');

    return view('admin.token-cards', compact('election', 'cards'));
})->name('token-cards');
