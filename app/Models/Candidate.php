<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    protected $fillable = ['election_id', 'number', 'name', 'running_mate', 'subtitle', 'tagline', 'vision', 'missions', 'programs', 'photo_path', 'sort_order'];

    protected function casts(): array
    {
        return ['missions' => 'array', 'programs' => 'array'];
    }

    public function election(): BelongsTo { return $this->belongsTo(Election::class); }
    public function ballots(): HasMany { return $this->hasMany(Ballot::class); }
}
