<?php

namespace App\Filament\Concerns;

use App\Models\Election;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

trait ResolvesElection
{
    use InteractsWithPageFilters;

    protected function election(): ?Election
    {
        $id = $this->pageFilters['election_id'] ?? null;

        return $id ? Election::find($id) : null;
    }
}