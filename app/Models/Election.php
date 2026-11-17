<?php

namespace App\Models;

use App\Enums\ElectionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Election extends Model
{
    /** @use HasFactory<\Database\Factories\ElectionFactory> */
    use HasFactory;

    protected $fillable = ['title', 'slug', 'description', 'organization', 'status', 'starts_at', 'ends_at', 'allow_blank_vote'];

    protected function casts(): array
    {
        return [
            'status' => ElectionStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'allow_blank_vote' => 'boolean',
        ];
    }

    public function candidates(): HasMany { return $this->hasMany(Candidate::class)->orderBy('sort_order'); }
    public function participations(): HasMany { return $this->hasMany(Participation::class); }
    public function ballots(): HasMany { return $this->hasMany(Ballot::class); }

    public function isOpen(): bool
    {
        return $this->status === ElectionStatus::Active
            && $this->starts_at 
            && $this->ends_at
            && now()->between($this->starts_at, $this->ends_at);
    }
}
