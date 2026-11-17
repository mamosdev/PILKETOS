<?php

namespace App\Livewire;

use App\Enums\ElectionStatus;
use App\Models\Election;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ElectionPortal extends Component
{
    public Election $election;

    public function mount(Election $election): void
    {
        // Pemilihan draf tidak boleh terlihat publik
        abort_if($election->status === ElectionStatus::Draft, 404);

        $this->election = $election;
    }

    #[Computed]
    public function candidates(): Collection
    {
        return $this->election->candidates;
    }

    #[Computed]
    public function modalData(): array
    {
        return $this->candidates->map(fn ($c) => [
            'id' => $c->id,
            'number' => $c->number,
            'name' => $c->display_name,
            'vision' => $c->vision,
            'missions' => collect($c->missions ?? [])
                ->map(fn ($m) => is_array($m) ? (string) (reset($m) ?: '') : $m)
                ->values()->all(),
            'programs' => $c->programs ?? [],
        ])->values()->all();
    }

    public function render()
    {
        return view('livewire.election-portal')->layout('components.layouts.app', [
            'title' => $this->election->title,
            'subtitle' => $this->election->organization,
        ]);
    }
}