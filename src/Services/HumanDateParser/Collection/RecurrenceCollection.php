<?php

namespace Services\HumanDateParser\Collection;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Services\HumanDateParser\Dto\RecurrenceDayDto;
use Services\HumanDateParser\Dto\RecurrenceMonthDto;
use Services\HumanDateParser\Dto\RecurrenceWeekDto;

class RecurrenceCollection extends Collection
{
    public function setDayRecurrence(Carbon $time): self
    {
        $time->setTimezone(config('app.timezone'));
        $this->push(new RecurrenceDayDto($time));
        return $this;
    }

    public function addMonthRecurrence(Carbon $time, int $dayOfMonth): self
    {
        $time->setTimezone(config('app.timezone'));

        if ($this->isEmpty()) {
            $this->push(new RecurrenceMonthDto($time, [$dayOfMonth]));
            return $this;
        }

        /** @var RecurrenceMonthDto $item */
        $item = $this->filter(function ($item) use ($time, $dayOfMonth) {
            if (
                $item->time->hour === $time->hour
                &&
                $item->time->minute === $time->minute
            ) {
                return true;
            }

            return false;
        })->first();

        if (empty($item)) {
            $this->push(new RecurrenceMonthDto($time, [$dayOfMonth]));
            return $this;
        }

        if (!in_array($dayOfMonth, $item->daysOfMonth)) {
            $this->transform(function (RecurrenceMonthDto $item) use ($time, $dayOfMonth) {
                if (
                    $item->time->hour === $time->hour
                    &&
                    $item->time->minute === $time->minute
                ) {
                    $newDays = array_merge([$dayOfMonth], $item->daysOfMonth);
                    return new RecurrenceMonthDto($time, $newDays);
                }

                return $item;
            });
        }

        return $this;
    }

    public function addWeekRecurrence(Carbon $time, int $dayOfWeek): self
    {
        $time->setTimezone(config('app.timezone'));

        if ($this->isEmpty()) {
            $this->push(new RecurrenceWeekDto($time, [$dayOfWeek]));
            return $this;
        }

        /** @var RecurrenceWeekDtoDto $item */
        $item = $this->filter(function ($item) use ($time, $dayOfWeek) {
            if (
                $item->time->hour === $time->hour
                &&
                $item->time->minute === $time->minute
            ) {
                return true;
            }

            return false;
        })->first();

        if (empty($item)) {
            $this->push(new RecurrenceWeekDto($time, [$dayOfWeek]));
            return $this;
        }

        if (!in_array($dayOfWeek, $item->daysOfWeek)) {
            $this->transform(function (RecurrenceWeekDto $item) use ($time, $dayOfWeek) {
                if (
                    $item->time->hour === $time->hour
                    &&
                    $item->time->minute === $time->minute
                ) {
                    $newDays = array_merge([$dayOfWeek], $item->daysOfWeek);
                    return new RecurrenceWeekDto($time, $newDays);
                }

                return $item;
            });
        }

        return $this;
    }
}
