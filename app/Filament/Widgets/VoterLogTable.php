<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesElection;
use App\Models\Participation;
use App\Models\Voter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class VoterLogTable extends TableWidget
{
    use ResolvesElection;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Log Pemilih')
            ->description('Hanya mencatat siapa yang sudah menggunakan hak suara, tidak pernah pilihannya.')
            ->query(fn (): Builder => Participation::query()
                ->with('voter')
                ->where('election_id', $this->election()?->id ?? 0))
            ->defaultSort('voted_at', 'desc')
            ->poll('15s')
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('voter.identifier')->label('NISN')->searchable(),
                TextColumn::make('voter.name')->label('Nama')->searchable(),
                TextColumn::make('voter.group_name')->label('Kelas')->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->state(fn (Participation $record) => $record->has_voted ? 'Sudah memilih' : 'Belum memilih')
                    ->badge()
                    ->color(fn (string $state) => $state === 'Sudah memilih' ? 'success' : 'gray'),
                TextColumn::make('voted_at')->label('Waktu')->dateTime('H:i')->placeholder('—')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('has_voted')
                    ->label('Status memilih')
                    ->trueLabel('Sudah memilih')
                    ->falseLabel('Belum memilih'),

                SelectFilter::make('group_name')
                    ->label('Kelas')
                    ->options(fn () => Voter::query()
                        ->whereHas('participations', fn ($q) => $q->where('election_id', $this->election()?->id ?? 0))
                        ->whereNotNull('group_name')
                        ->distinct()
                        ->orderBy('group_name')
                        ->pluck('group_name', 'group_name'))
                    ->query(fn (Builder $query, array $data) => filled($data['value'] ?? null)
                        ? $query->whereHas('voter', fn ($v) => $v->where('group_name', $data['value']))
                        : $query),
            ]);
    }
}