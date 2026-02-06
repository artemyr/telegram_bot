<?php

namespace Domain\Travel\Models;

use Domain\Travel\Database\Factories\TravelStyleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelStyle extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return TravelStyleFactory::new();
    }

    public function travelQuestionnaire(): HasMany
    {
        return $this->hasMany(TravelQuestionnaire::class);
    }
}
