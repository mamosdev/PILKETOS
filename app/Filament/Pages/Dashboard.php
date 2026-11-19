<?php

namespace App\Filament\Pages;

use App\Enums\ElectionStatus;
use App\Filament\Widgets\CohortParticipationChart;
use App\Filament\Widgets\ElectionStatsOverview;
use App\Filament\Widgets\VoterLogTable;
use App\Filament\Widgets\VoteResultsChart;
use App\Models\Election;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function getTitle(): string
    {
        return 'Dashboard Panitia';
    }

    public function getColumns(): int|array
    {
        return ['md' => 2];
    }

    public function getWidgets(): array
    {
        return [
            ElectionStatsOverview::class,
            VoteResultsChart::class,
            CohortParticipationChart::class,
            VoterLogTable::class,
        ];
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Select::make('election_id')
                    ->label('Pemilihan')
                    ->options(fn () => Election::query()->latest('starts_at')->pluck('title', 'id'))
                    ->default(fn () => Election::query()->latest('starts_at')->value('id'))
                    ->selectablePlaceholder(false),
            ]),
        ]);
    }

    protected function selectedElection(): ?Election
    {
        return Election::find($this->filters['election_id'] ?? null);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('closeElection')
                ->label('Tutup Pemilihan')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Tutup pemilihan sekarang?')
                ->modalDescription('Pemungutan suara dihentikan dan hasil akhir langsung ditampilkan. Pemilihan yang sudah ditutup tidak dapat dibuka kembali.')
                ->visible(fn () => $this->selectedElection()?->phase() === 'open')
                ->action(function () {
                    $this->selectedElection()?->update(['status' => ElectionStatus::Closed]);

                    Notification::make()->title('Pemilihan ditutup')->success()->send();
                }),
        ];
    }
}