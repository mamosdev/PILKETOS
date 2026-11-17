<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ballot extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['id', 'election_id', 'candidate_id', 'receipt_code'];

    public function election(): BelongsTo { return $this->belongsTo(Election::class); }
    public function candidate(): BelongsTo { return $this->belongsTo(Candidate::class); }
}
