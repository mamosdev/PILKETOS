<?php

namespace App\Services;

use App\Exceptions\VoteException;
use App\Models\Ballot;
use App\Models\Election;
use App\Models\Participation;
use App\Support\TokenGenerator;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoteService
{
    /**
     * Mencatat satu suara. Mengembalikan kode e-receipt.
     * $candidateId = null berarti suara kosong.
     */
    public function cast(Participation $participation, ?int $candidateId): string
    {
        // Selalu baca ulang dari database: data dari browser dan objek lama tidak dipercaya
        $election = Election::findOrFail($participation->election_id);

        if ($election->phase() !== 'open') {
            throw new VoteException('Pemungutan suara sedang tidak dibuka.');
        }

        if ($candidateId === null) {
            if (! $election->allow_blank_vote) {
                throw new VoteException('Pilihan tidak valid.');
            }
        } elseif (! $election->candidates()->whereKey($candidateId)->exists()) {
            throw new VoteException('Pilihan tidak valid.');
        }

        for ($attempt = 1; ; $attempt++) {
            try {
                return DB::transaction(function () use ($participation, $election, $candidateId) {
                    // Klaim hak suara secara atomik. Kalau dua request datang bersamaan,
                    // InnoDB mengunci baris ini: yang kedua menunggu, lalu melihat
                    // has_voted = true dan mendapat 0 baris.
                    $claimed = Participation::query()
                        ->whereKey($participation->getKey())
                        ->where('election_id', $election->id)
                        ->where('has_voted', false)
                        ->update(['has_voted' => true, 'voted_at' => now()]);

                    if ($claimed !== 1) {
                        throw new VoteException('Hak suara kamu sudah digunakan.');
                    }

                    // Tidak ada voter_id, participation_id, atau timestamp di sini
                    return Ballot::create([
                        'id' => (string) Str::uuid(),
                        'election_id' => $election->id,
                        'candidate_id' => $candidateId,
                        'receipt_code' => $this->newReceiptCode(),
                    ])->receipt_code;
                }, 3);
            } catch (UniqueConstraintViolationException $e) {
                // Kode e-receipt kebetulan kembar: transaksi sudah dibatalkan, coba lagi
                if ($attempt >= 3) {
                    throw $e;
                }
            }
        }
    }

    private function newReceiptCode(): string
    {
        $raw = TokenGenerator::make(8);

        return 'PK'.now()->format('y').'-'.substr($raw, 0, 4).'-'.substr($raw, 4, 4);
    }
}