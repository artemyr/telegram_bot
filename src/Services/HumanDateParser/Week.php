<?php

namespace Services\HumanDateParser;

use Illuminate\Support\Collection;

class Week
{
    protected array $weekWords;

    public function __construct()
    {
        $weekWords = config('humandateparser.week_words', []);
        $this->weekWords = Collection::make($weekWords)
            ->map(function ($item) {
                return mb_strtolower($item);
            })->toArray();
    }

    public function handle(array $date, $next)
    {
        foreach ($this->weekWords as $key => $week) {
            if (str_contains($date['startString'], $week)) {
                $date['type'] = 'weekly';
                $date['rule'][$key] = 'time';
            }
        }

        return $next($date);
    }
}
