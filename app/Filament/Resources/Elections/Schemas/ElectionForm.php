<?php

namespace App\Filament\Resources\Elections\Schemas;

use App\Models\Election;
use App\Enums\ElectionStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ElectionForm
{
    public static function configure(Schema $schema): Schema
    {
        $locked = fn (?Election $record) => $record?->phase() === 'closed';
        return $schema->components([
            Section::make('Informasi pemilihan')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Judul')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                            if ($operation === 'create') {
                                $set('slug', Str::slug($state ?? ''));
                            }
                        }),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    TextInput::make('organization')
                        ->label('Lembaga / Sekolah')
                        ->maxLength(255),
                    Select::make('status')
                        ->options(ElectionStatus::class)
                        ->default(ElectionStatus::Draft)
                        ->required()
                        ->disabled($locked)
                        ->helperText('Pemilihan yang sudah ditutup tidak dapat dibuka kembali.'),
                    Textarea::make('description')
                        ->label('Deskripsi')
                        ->columnSpanFull(),
                ]),

            Section::make('Jadwal & aturan')
                ->columns(2)
                ->schema([
                    DateTimePicker::make('starts_at')->label('Mulai')->seconds(false)->required()->disabled($locked),
                    DateTimePicker::make('ends_at')->label('Selesai')->seconds(false)->required()->after('starts_at')->disabled($locked),
                    Toggle::make('allow_blank_vote')->label('Izinkan suara kosong')->disabled($locked),
                    Toggle::make('show_live_results')
                        ->label('Tampilkan hasil sementara selama pemungutan suara')
                        ->helperText('Tidak disarankan. Jika aktif, panitia yang memantau dashboard sambil melihat log pemilih bisa menebak pilihan seseorang dari perubahan angka. Biarkan mati agar hasil baru terlihat setelah pemilihan ditutup.')
                        ->disabled($locked)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}