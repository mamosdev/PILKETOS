<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Candidate extends Model
{
    protected $fillable = ['election_id', 'number', 'name', 'running_mate', 'subtitle', 'tagline', 'vision', 'missions', 'programs', 'photo_path', 'sort_order'];

    protected function casts(): array
    {
        return ['missions' => 'array', 'programs' => 'array'];
    }

    public function election(): BelongsTo { return $this->belongsTo(Election::class); }
    public function ballots(): HasMany { return $this->hasMany(Ballot::class); }

    public function getDisplayNameAttribute(): string
    {
        return $this->running_mate ? "{$this->name} & {$this->running_mate}" : $this->name;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null;
    }

    public function getAccentGradientAttribute(): string
    {
        $gradients = [
            'from-blue-800 to-indigo-700',
            'from-indigo-700 to-sky-700',
            'from-slate-800 to-blue-900',
        ];

        return $gradients[max(0, $this->sort_order - 1) % count($gradients)];
    }
}
