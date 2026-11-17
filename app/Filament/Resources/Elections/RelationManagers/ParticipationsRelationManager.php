<?php

namespace App\Filament\Resources\Elections\RelationManagers;

use App\Filament\Imports\VoterImporter;
use App\Models\Participation;
use App\Support\TokenGenerator;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ImportAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class ParticipationsRelationManager extends RelationManager
{
    protected static string $relationship = 'participations';
    protected static ?string $title = 'Daftar Pemilih (DPT)';

    public function table(Table $table): Table
    {
        $election = $this->getOwnerRecord();

        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('voter.identifier')->label('NISN')->searchable(),
                TextColumn::make('voter.name')->label('Nama')->searchable(),
                TextColumn::make('voter.group_name')->label('Kelas')->searchable(),
                IconColumn::make('has_token')
                    ->label('Token')
                    ->boolean()
                    ->state(fn (Participation $record) => filled($record->token_hash)),
                IconColumn::make('has_voted')->label('Sudah memilih')->boolean(),
                TextColumn::make('voted_at')->label('Waktu')->dateTime('H:i')->placeholder('-'),
            ])
            ->filters([
                TernaryFilter::make('has_voted')->label('Status memilih')
                    ->trueLabel('Sudah memilih')->falseLabel('Belum memilih'),
            ])
            ->headerActions([
                ImportAction::make()
                    ->label('Impor CSV')
                    ->importer(VoterImporter::class)
                    ->options(['election_id' => $election->getKey()]),

                Action::make('issueTokens')
                    ->label('Terbitkan Token')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Token dibuat untuk pemilih yang belum punya token, lalu halaman kartu token dibuka. Token asli hanya tampil sekali dan tidak disimpan di database.')
                    ->action(function () use ($election) {
                        set_time_limit(0);

                        $cards = [];

                        $election->participations()
                            ->whereNull('token_hash')
                            ->with('voter')
                            ->chunkById(200, function ($chunk) use (&$cards) {
                                foreach ($chunk as $participation) {
                                    $token = TokenGenerator::make();

                                    $participation->update([
                                        'token_hash' => TokenGenerator::hash($token),
                                        'token_issued_at' => now(),
                                    ]);

                                    $cards[] = [
                                        'name' => $participation->voter->name,
                                        'group' => $participation->voter->group_name,
                                        'identifier' => $participation->voter->identifier,
                                        'token' => $token,
                                    ];
                                }
                            });

                        if (empty($cards)) {
                            Notification::make()->title('Semua pemilih sudah punya token')->warning()->send();

                            return;
                        }

                        Cache::put('token-cards:'.$election->id.':'.auth()->id(), $cards, now()->addMinutes(30));

                        return $this->redirect(route('token-cards', $election));
                    }),
            ])
            ->recordActions([
                Action::make('resetToken')
                    ->label('Reset token')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn (Participation $record) => ! $record->has_voted)
                    ->action(function (Participation $record) {
                        $token = TokenGenerator::make();

                        $record->update([
                            'token_hash' => TokenGenerator::hash($token),
                            'token_issued_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Token baru untuk '.$record->voter->name)
                            ->body("Token: {$token}")
                            ->success()
                            ->persistent()
                            ->send();
                    }),

                // Peserta yang sudah memilih tidak boleh dihapus: kalau dihapus lalu
                // didaftarkan ulang, ia bisa memilih dua kali.
                DeleteAction::make()->hidden(fn (Participation $record) => $record->has_voted),
            ]);
    }
}