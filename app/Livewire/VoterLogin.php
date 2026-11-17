<?php

namespace App\Livewire;

use App\Enums\ElectionStatus;
use App\Models\Election;
use App\Models\Participation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class VoterLogin extends Component
{
    public Election $election;

    public string $identifier = '';
    public string $token = '';

    public function mount(Election $election)
    {
        abort_if($election->status === ElectionStatus::Draft, 404);

        $this->election = $election;

        $current = Auth::guard('voter')->user();

        if ($current && (int) $current->election_id === $election->id && ! $current->has_voted) {
            return $this->redirectRoute('booth', $election);
        }
    }

    public function login()
    {
        $this->validate([
            'identifier' => ['required', 'string', 'max:30'],
            'token' => ['required', 'string', 'max:12'],
        ], [
            'identifier.required' => 'NISN wajib diisi.',
            'token.required' => 'Token wajib diisi.',
        ]);

        if ($this->election->phase() !== 'open') {
            $this->addError('identifier', 'Pemungutan suara sedang tidak dibuka.');

            return;
        }

        $nisn = trim($this->identifier);
        $nisnKey = 'voter-nisn:'.$this->election->id.'|'.Str::lower($nisn);
        $ipKey = 'voter-ip:'.request()->ip();

        if (RateLimiter::tooManyAttempts($nisnKey, 5) || RateLimiter::tooManyAttempts($ipKey, 60)) {
            $seconds = max(RateLimiter::availableIn($nisnKey), RateLimiter::availableIn($ipKey));

            $this->addError('token', 'Terlalu banyak percobaan. Coba lagi dalam '.ceil($seconds / 60).' menit.');

            return;
        }

        $participation = Participation::query()
            ->where('election_id', $this->election->id)
            ->whereHas('voter', fn ($q) => $q->where('identifier', $nisn))
            ->first();

        if (! $participation || ! $participation->verifyToken($this->token)) {
            RateLimiter::hit($nisnKey, 300);
            RateLimiter::hit($ipKey, 60);

            $this->addError('token', 'NISN atau token tidak cocok. Periksa kembali kartu token kamu.');

            return;
        }

        // Token benar. Baru di sini status "sudah memilih" boleh diungkap.
        if ($participation->has_voted) {
            $this->addError('identifier', 'Hak suara untuk NISN ini sudah digunakan.');

            return;
        }

        RateLimiter::clear($nisnKey);

        Auth::guard('voter')->login($participation);
        session()->regenerate();

        return $this->redirectRoute('booth', $this->election);
    }

    public function render()
    {
        return view('livewire.voter-login')->layout('components.layouts.app', [
            'title' => 'Masuk Bilik Suara — '.$this->election->title,
            'subtitle' => $this->election->organization,
        ]);
    }
}