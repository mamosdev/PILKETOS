<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    /** @use HasFactory<\Database\Factories\VoterFactory> */
    use HasFactory;

    protected $fillable = ['identifier', 'name', 'group_name', 'cohort'];

public function participations(): HasMany { return $this->hasMany(Participation::class); }
}
