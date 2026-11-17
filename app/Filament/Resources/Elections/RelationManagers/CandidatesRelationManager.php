<?php

namespace App\Filament\Resources\Elections\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CandidatesRelationManager extends RelationManager
{
    protected static string $relationship = 'candidates';
    protected static ?string $title = 'Kandidat';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas')->columns(2)->schema([
                TextInput::make('number')->label('No. urut')->required()->maxLength(8),
                TextInput::make('name')->label('Nama (Ketua / Opsi)')->required()->maxLength(255),
                TextInput::make('running_mate')->label('Wakil (opsional)')->maxLength(255),
                TextInput::make('subtitle')->label('Keterangan / Kelas')->maxLength(255),
                TextInput::make('tagline')->maxLength(255)->columnSpanFull(),
                FileUpload::make('photo_path')
                    ->label('Foto')
                    ->image()
                    ->disk('public')
                    ->directory('candidates')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->columnSpanFull(),
            ]),
            Section::make('Visi, misi & program')->schema([
                Textarea::make('vision')->label('Visi')->rows(3),
                Repeater::make('missions')
                    ->label('Misi')
                    ->simple(TextInput::make('misi')->required())
                    ->addActionLabel('Tambah misi'),
                Repeater::make('programs')
                    ->label('Program unggulan')
                    ->schema([
                        TextInput::make('title')->label('Nama program')->required(),
                        Textarea::make('desc')->label('Deskripsi')->rows(2),
                    ])
                    ->addActionLabel('Tambah program'),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('number')->label('No.')->badge(),
                ImageColumn::make('photo_path')->label('Foto')->disk('public')->circular(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('running_mate')->label('Wakil'),
                TextColumn::make('subtitle')->label('Keterangan'),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([
                EditAction::make(),
                // Kandidat yang sudah punya suara tidak boleh dihapus (lihat restrictOnDelete)
                DeleteAction::make()->hidden(fn ($record) => $record->ballots()->exists()),
            ]);
    }
}