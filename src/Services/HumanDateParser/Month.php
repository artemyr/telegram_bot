<?php

namespace Services\HumanDateParser;

class Month
{
    protected string $monthDayWord;

    public function __construct()
    {
        $this->monthDayWord = config('humandateparser.key_words.month_day', '');
    }

    public function handle(array $date, $next)
    {
        if (str_contains($date['startString'], $this->monthDayWord)) {
            $date['type'] = 'monthly';
            $date['rule'] = [];
        }

        return $next($date);
    }

}
