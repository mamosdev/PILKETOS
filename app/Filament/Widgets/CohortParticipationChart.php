<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesElection;
use Filament\Widgets\ChartWidget;

class CohortParticipationChart extends ChartWidget
{
    use ResolvesElection;

    protected ?string $pollingInterval = '15s';

    protected ?string $maxHeight = '420px';

    public function getHeading(): ?string
    {
        return 'Partisipasi per Kelas';
    }

    public function getDescription(): ?string
    {
        return 'Persentase pemilih yang sudah menggunakan hak suara.';
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $election = $this->election();

        if (! $election) {
            return ['datasets' => [], 'labels' => []];
        }

        $rows = $election->participations()
            ->join('voters', 'voters.id', '=', 'participations.voter_id')
            ->selectRaw('voters.group_name as grp, count(*) as total, sum(participations.has_voted) as voted')
            ->groupBy('voters.group_name')
            ->orderBy('grp')
            ->get();

        return [
            'datasets' => [[
                'label' => 'Partisipasi (%)',
                'data' => $rows->map(fn ($r) => $r->total ? round($r->voted / $r->total * 100, 1) : 0)->all(),
                'backgroundColor' => '#1e40af',
                'borderRadius' => 6,
            ]],
            'labels' => $rows->map(fn ($r) => $r->grp ?: 'Tanpa kelas')->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['x' => ['min' => 0, 'max' => 100]],
        ];
    }
}