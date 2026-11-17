<?php

namespace App\Filament\Imports;

use App\Models\Participation;
use App\Models\Voter;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class VoterImporter extends Importer
{
    protected static ?string $model = Voter::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('identifier')
                ->label('NISN')
                ->guess(['nisn', 'NISN', 'nis'])
                ->exampleHeader('NISN')
                ->example('0090000001')
                ->requiredMapping()
                ->rules(['required', 'max:30']),
            ImportColumn::make('name')
                ->label('Nama')
                ->guess(['nama', 'name'])
                ->example('Aisyah Nur')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('group_name')
                ->label('Kelas')
                ->guess(['kelas', 'rombel'])
                ->example('X MIPA 1')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('cohort')
                ->label('Angkatan')
                ->guess(['angkatan', 'tingkat'])
                ->example('X')
                ->rules(['nullable', 'max:10']),
        ];
    }

    public function resolveRecord(): Voter
    {
        // NISN yang sama tidak diduplikasi, datanya diperbarui
        return Voter::firstOrNew(['identifier' => $this->data['identifier']]);
    }

    protected function afterSave(): void
    {
        // Daftarkan sebagai peserta pemilihan terpilih, tanpa token dulu
        Participation::firstOrCreate([
            'election_id' => $this->options['election_id'],
            'voter_id' => $this->record->getKey(),
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = number_format($import->successful_rows).' pemilih berhasil diimpor.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failed).' baris gagal.';
        }

        return $body;
    }
}