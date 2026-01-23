<?php

namespace Services\HumanDateParser\Parsers;

use Carbon\Carbon;
use Domain\Schedule\Tasks\Enum\TaskRepeatTypesEnum;
use Illuminate\Support\Collection;
use Support\Contracts\HumanDateParserContract;

/**
 * Понедельник 10:00 Среда 17:00
 * каждый вторник в 7:00
 */
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

    public function handle(HumanDateParserContract $date, $next)
    {
        if ($this->isCurrent($date)) {
            $this->process($date);
        }

        return $next($date);
    }

    protected function isCurrent(HumanDateParserContract $date): bool
    {
        foreach ($this->weekWords as $dayOfWeek) {
            if (str_contains($date->getStartString(), $dayOfWeek)) {
                $date->setType(TaskRepeatTypesEnum::WEEKLY);
                return true;
            }
        }

        return false;
    }

    protected function process(HumanDateParserContract $date): void
    {
        foreach ($this->weekWords as $key => $dayOfWeek) {
            if (str_contains($date->getStartString(), $dayOfWeek)) {
                if (preg_match("~$dayOfWeek в (\d{2}):(\d{2})~", $date->getStartString(), $matches)) {
                    $date->getCollection()
                        ->addWeekRecurrence(
                            Carbon::createFromTime(
                                $matches[1],
                                $matches[2],
                                0,
                                $date->getTimezone()
                            ),
                            $key
                        );
                }
            }
        }
    }
}
