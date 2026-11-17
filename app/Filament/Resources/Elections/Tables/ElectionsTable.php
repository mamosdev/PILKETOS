<?php

namespace App\Filament\Resources\Elections\Tables;

use App\Enums\ElectionStatus;
use App\Models\Election;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ElectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Judul')->searchable()->weight('bold'),
                TextColumn::make('status')->badge(),
                TextColumn::make('starts_at')->label('Mulai')->dateTime('d M Y, H:i'),
                TextColumn::make('ends_at')->label('Selesai')->dateTime('d M Y, H:i'),
                TextColumn::make('participations_count')->label('DPT')->counts('participations'),
            ])
            ->recordActions([
                EditAction::make(),
                // Hapus pemilihan akan ikut menghapus semua suara (cascade),
                // jadi hanya boleh untuk yang masih draf.
                DeleteAction::make()
                    ->hidden(fn (Election $record) => $record->status !== ElectionStatus::Draft),
            ]);
    }
}