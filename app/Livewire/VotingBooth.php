<?php

namespace App\Livewire;

use App\Exceptions\VoteException;
use App\Models\Election;
use App\Models\Participation;
use App\Services\VoteService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class VotingBooth extends Component
{
    #[Locked]
    public Election $election;

    #[Locked]
    public ?string $receiptCode = null;

    #[Locked]
    public ?string $votedAt = null;

    #[Locked]
    public ?string $voterName = null;

    public function mount(Election $election): void
    {
        $this->election = $election;
    }

    #[Computed]
    public function participation(): Participation
    {
        return Auth::guard('voter')->user()->load('voter');
    }

    #[Computed]
    public function candidates(): Collection
    {
        return $this->election->candidates;
    }

    /**
     * $choice: id kandidat, atau 'blank' untuk suara kosong.
     * Nilai ini datang dari browser, jadi divalidasi ulang di VoteService.
     */
    public function submit(mixed $choice = null): void
    {
        $participation = Auth::guard('voter')->user();

        // Aksi Livewire tidak melewati middleware route, jadi dicek ulang di sini
        abort_unless($participation && (int) $participation->election_id === $this->election->id, 403);

        $this->resetErrorBag();

        if ($choice === 'blank') {
            $candidateId = null;
        } elseif (is_numeric($choice)) {
            $candidateId = (int) $choice;
        } else {
            $this->addError('vote', 'Pilih salah satu pasangan calon terlebih dahulu.');

            return;
        }

        try {
            $code = app(VoteService::class)->cast($participation, $candidateId);
        } catch (VoteException $e) {
            $this->addError('vote', $e->getMessage());

            return;
        }

        // Simpan yang perlu ditampilkan, lalu keluarkan pemilih dari perangkat ini
        $this->receiptCode = $code;
        $this->votedAt = now()->translatedFormat('d F Y, H:i').' WIB';
        $this->voterName = $participation->voter->name;

        Auth::guard('voter')->logout();
        session()->regenerate();
    }

    public function render()
    {
        $labels = $this->candidates
            ->mapWithKeys(fn ($c) => [$c->id => 'Paslon '.$c->number.' — '.$c->display_name])
            ->when($this->election->allow_blank_vote, fn ($c) => $c->put('blank', 'Suara Kosong'))
            ->all();

        return view('livewire.voting-booth', ['labels' => $labels])
            ->layout('components.layouts.app', [
                'title' => 'Bilik Suara — '.$this->election->title,
                'subtitle' => $this->election->organization,
            ]);
    }
}