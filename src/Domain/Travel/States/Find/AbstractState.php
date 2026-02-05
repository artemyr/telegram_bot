<?php

namespace Domain\Travel\States\Find;

use Domain\TelegramBot\BotState;
use Domain\Travel\Models\TravelClaim;

abstract class AbstractState extends BotState
{
    protected function claimExists(): bool
    {
        return TravelClaim::query()
            ->where('telegram_user_id', nutgram()->userId())
            ->exists();
    }

    protected function getClaim(): ?TravelClaim
    {
        return TravelClaim::query()
            ->select(['id', 'telegram_user_id', 'date_from', 'date_to', 'travel_format_id', 'travel_resort_id'])
            ->where('telegram_user_id', nutgram()->userId())
            ->with('travelFormat')
            ->with('travelResort')
            ->first();
    }

    protected function createClaim(): ?TravelClaim
    {
        return TravelClaim::query()
            ->firstOrCreate([
                'telegram_user_id' => nutgram()->userId(),
            ]);
    }

    protected function validate(string $value, array $keyboard): bool
    {
        $variants = [];

        foreach ($keyboard as $item) {
            if (is_array($item)) {
                foreach ($item as $button) {
                    $variants[] = $button;
                }
            } else {
                $variants[] = $item;
            }
        }

        return in_array($value, $variants, true);
    }
}
