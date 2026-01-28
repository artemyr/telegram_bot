<?php

namespace Domain\Schedule\Calendar\States;

use Domain\Schedule\Calendar\Models\Timer;
use Domain\Schedule\Calendar\Presentations\TimerPresentation;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\Exceptions\PrintableException;
use Domain\TelegramBot\MenuBotState;

class CalendarListState extends BotState
{
    public function render(): void
    {
        $timers = Timer::query()
            ->where('telegram_user_id', nutgram()->userId())
            ->get();

        message()
            ->text([
                "Раздел: Календарь",
                "Напишите номер тамймера, чтобы его отменить",
                "Список событий:",
                (string)(new TimerPresentation($timers, tusertimezone()))
            ])
            ->inlineKeyboard(keyboard()->back())
            ->send();
    }

    /**
     * @throws PrintableException
     */
    public function handle(): BotState
    {
        if (nutgram()->isCallbackQuery()) {
            $query = nutgram()->callbackQuery()->data;

            if ($query === KeyboardEnum::BACK->value) {
                keyboard()->remove();
                return new MenuBotState(troute('calendar'));
            }
        } else {
            $query = nutgram()->message()?->getText();

            if (filter_var($query, FILTER_VALIDATE_INT)) {
                $timers = Timer::query()
                    ->where('telegram_user_id', nutgram()->userId())
                    ->get();
                $table = (new TimerPresentation($timers))->getTable();

                $row = $table->getRow((int)$query);

                if (empty($row)) {
                    throw new PrintableException('Выберите из списка');
                }

                $timer = Timer::query()
                    ->where('id', $row->getCol('id'))
                    ->first();

                if($timer) {
                    $timer->delete();
                    message("Таймер \"{$timer->title}\" удален");
                } else {
                    message("Таймер не найден");
                }
                return $this;
            }
        }
    }
}
