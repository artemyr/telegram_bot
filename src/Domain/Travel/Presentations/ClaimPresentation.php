<?php

namespace Domain\Travel\Presentations;

use Domain\Travel\Models\TravelClaim;
use Support\Traits\Makeable;

class ClaimPresentation
{
    use Makeable;

    public function __construct(protected TravelClaim $claim)
    {
    }

    public function textMessage(): string
    {
        $text = [];

        $where = $this->claim->travelResort?->title;
        $from = $this->claim->date_from;
        $to = $this->claim->date_to;
        $how = $this->claim->travelFormat?->title;

        $text[] = "Где: " . ($where ?: '...');
        $text[] = "Когда: с " . ($from ?: '...');
        $text[] = "по " . ($to ?: '...');
        $text[] = "Как: " . ($how ?: '...');

        return implode("\n", $text);
    }
}
