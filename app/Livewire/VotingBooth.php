<?php

namespace App\Livewire;

use App\Models\Election;
use App\Models\Participation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class VotingBooth extends Component
{
    public Election $election;

    public function mount(Election $election): void
    {
        $this->election = $election;
    }

    // Sengaja computed, bukan public property: data pemilih tidak ikut
    // dikirim ke browser dan tidak bisa dimanipulasi dari sisi klien.
    #[Computed]
    public function participation(): Participation
    {
        return Auth::guard('voter')->user()->load('voter');
    }

    public function render()
    {
        return view('livewire.voting-booth')->layout('components.layouts.app', [
            'title' => 'Bilik Suara — '.$this->election->title,
            'subtitle' => $this->election->organization,
        ]);
    }
}