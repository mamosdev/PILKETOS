<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participation extends Model
{
    protected $fillable = ['election_id', 'voter_id', 'token_hash', 'token_issued_at', 'has_voted', 'voted_at'];
    protected $hidden = ['token_hash'];

    protected function casts(): array
    {
        return ['has_voted' => 'boolean', 'voted_at' => 'datetime', 'token_issued_at' => 'datetime'];
    }

    public function election(): BelongsTo { return $this->belongsTo(Election::class); }
    public function voter(): BelongsTo { return $this->belongsTo(Voter::class); }

    public function verifyToken(string $token): bool
    {
        return filled($this->token_hash)
            && Hash::check(strtoupper(trim($token)), $this->token_hash);
    }
}
