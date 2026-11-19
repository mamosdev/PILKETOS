<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesElection;
use Filament\Widgets\ChartWidget;

class VoteResultsChart extends ChartWidget
{
    use ResolvesElection;

    protected ?string $pollingInterval = '10s';

    protected ?string $maxHeight = '320px';

    public function getHeading(): ?string
    {
        return 'Perolehan Suara';
    }

    public function getDescription(): ?string
    {
        $election = $this->election();

        if (! $election) {
            return null;
        }

        return $election->resultsVisible()
            ? 'Dihitung dari surat suara anonim.'
            : 'Hasil dirahasiakan sampai pemungutan suara ditutup.';
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $election = $this->election();

        if (! $election || ! $election->resultsVisible()) {
            return ['datasets' => [], 'labels' => []];
        }

        $counts = $election->ballots()
            ->whereNotNull('candidate_id')
            ->selectRaw('candidate_id, count(*) as total')
            ->groupBy('candidate_id')
            ->pluck('total', 'candidate_id');

        $labels = [];
        $data = [];

        foreach ($election->candidates as $candidate) {
            $labels[] = 'Paslon '.$candidate->number;
            $data[] = (int) ($counts[$candidate->id] ?? 0);
        }

        if ($election->allow_blank_vote) {
            $labels[] = 'Suara Kosong';
            $data[] = $election->ballots()->whereNull('candidate_id')->count();
        }

        $palette = ['#1e40af', '#4338ca', '#0369a1', '#334155', '#b45309'];

        return [
            'datasets' => [[
                'label' => 'Suara',
                'data' => $data,
                'backgroundColor' => array_slice(array_pad($palette, count($data), '#64748b'), 0, count($data)),
                'borderRadius' => 8,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}