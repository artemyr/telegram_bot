<?php

namespace Services\HumanDateParser\Parsers;

use Carbon\Carbon;
use Domain\Tasks\Enum\TaskRepeatTypesEnum;
use Support\Contracts\HumanDateParserContract;

/**
 * каждый день в 10:00
 */
class Day
{
    protected string $everyDayWord;

    public function __construct()
    {
        $this->everyDayWord = config('humandateparser.key_words.every_day', '');
    }

    public function handle(HumanDateParserContract $date, $next)
    {
        if (str_contains($date->getStartString(), $this->everyDayWord)) {
            $date->setType(TaskRepeatTypesEnum::DAILY);

            if (preg_match("~(\d{2}):(\d{2})~", $date->getStartString(), $matches)) {
                $date->getCollection()->setDayRecurrence(
                    Carbon::createFromTime(
                        $matches[1],
                        $matches[2],
                        0,
                        $date->getTimezone()
                    )
                );
            }
        }

        return $next($date);
    }

}
