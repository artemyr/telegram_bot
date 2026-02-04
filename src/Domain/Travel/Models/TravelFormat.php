<?php

namespace Domain\Travel\Models;

use Domain\Travel\Factories\TravelFormatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelFormat extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return TravelFormatFactory::new();
    }
}
