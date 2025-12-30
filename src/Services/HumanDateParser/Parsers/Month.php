<?php

namespace Services\HumanDateParser\Parsers;

use Carbon\Carbon;
use Domain\Tasks\Enum\TaskRepeatTypesEnum;
use Support\Contracts\HumanDateParserContract;

/**
 * TODO 1,5,10 числа в 14:00
 *
 * 1 числа в 13:00
 * 4 числа в 18:00
 */
class Month
{
    protected string $monthDayWord;

    public function __construct()
    {
        $this->monthDayWord = config('humandateparser.key_words.month_day', '');
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
        if (str_contains($date->getStartString(), $this->monthDayWord)) {
            $date->setType(TaskRepeatTypesEnum::MONTHLY);
            return true;
        }

        return false;
    }

    protected function process(HumanDateParserContract $date): void
    {
        if (preg_match_all("~(?P<day>\d) числа в (?P<hour>\d{2}):(?P<minute>\d{2})~", $date->getStartString(), $matches)) {
            foreach ($matches[0] as $key => $match) {
                $date->getCollection()
                    ->addMonthRecurrence(
                        Carbon::createFromTime(
                            $matches['hour'][$key],
                            $matches['minute'][$key],
                            0,
                            $date->getTimezone()
                        ),
                        $matches['day'][$key]
                    );
            }
        }
    }
}
