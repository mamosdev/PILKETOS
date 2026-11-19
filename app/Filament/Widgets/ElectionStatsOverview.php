<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesElection;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ElectionStatsOverview extends StatsOverviewWidget
{
    use ResolvesElection;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $election = $this->election();

        if (! $election) {
            return [];
        }

        $total = $election->participations()->count();
        $voted = $election->participations()->where('has_voted', true)->count();
        $ballots = $election->ballots()->count();
        $pct = $total ? round($voted / $total * 100, 1) : 0;

        $phaseLabel = match ($election->phase()) {
            'open' => 'Pemungutan suara berlangsung',
            'upcoming' => 'Pemungutan suara belum dibuka',
            default => 'Pemungutan suara ditutup',
        };

        // Rekonsiliasi: jumlah surat suara harus sama dengan jumlah pemilih yang tercatat memilih
        $matches = $ballots === $voted;

        return [
            Stat::make('Daftar Pemilih Tetap', number_format($total, 0, ',', '.'))
                ->description('Terdaftar di pemilihan ini'),

            Stat::make('Suara Masuk', number_format($voted, 0, ',', '.'))
                ->description($matches
                    ? 'Terverifikasi: jumlah surat suara cocok dengan log'
                    : 'PERINGATAN: jumlah surat suara ('.$ballots.') tidak sama dengan log')
                ->color($matches ? 'success' : 'danger'),

            Stat::make('Belum Memilih', number_format($total - $voted, 0, ',', '.'))
                ->description('Masih memiliki hak suara')
                ->color('warning'),

            Stat::make('Partisipasi', number_format($pct, 1, ',', '.').'%')
                ->description($phaseLabel),
        ];
    }
}