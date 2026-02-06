<?php

namespace Domain\Travel\Models;

use Domain\Travel\Database\Factories\TravelStyleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TravelStyle extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return TravelStyleFactory::new();
    }

    public function travelQuestionnaire(): BelongsToMany
    {
        return $this->belongsToMany(TravelQuestionnaire::class);
    }
}
