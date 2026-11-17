<?php

namespace App\Livewire;

use App\Models\Election;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ParticipationStats extends Component
{
    public Election $election;

    public function render()
    {
        // Cache 10 detik: ratusan pemilih yang membuka portal bersamaan
        // tidak menjalankan query agregat yang sama berulang-ulang.
        $data = Cache::remember('stats:'.$this->election->id, 10, function () {
            $rows = $this->election->participations()
                ->join('voters', 'voters.id', '=', 'participations.voter_id')
                ->selectRaw('voters.cohort as cohort, count(*) as total, sum(participations.has_voted) as voted')
                ->groupBy('voters.cohort')
                ->get();

            $total = (int) $rows->sum('total');
            $voted = (int) $rows->sum('voted');
            $order = ['X', 'XI', 'XII'];

            $cohorts = $rows->filter(fn ($r) => filled($r->cohort))
                ->sortBy(fn ($r) => ($i = array_search($r->cohort, $order, true)) === false ? 99 : $i)
                ->mapWithKeys(fn ($r) => [
                    $r->cohort => $r->total ? round($r->voted / $r->total * 100, 1) : 0,
                ]);

            return [
                'total' => $total,
                'voted' => $voted,
                'pct' => $total ? round($voted / $total * 100, 1) : 0,
                'cohorts' => $cohorts->all(),
            ];
        });

        return view('livewire.participation-stats', $data);
    }
}