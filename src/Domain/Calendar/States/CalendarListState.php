<?php

namespace Domain\Calendar\States;

use Domain\Calendar\Models\Timer;
use Domain\Calendar\Presentations\TimerPresentation;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\Exceptions\PrintableException;
use Domain\TelegramBot\MenuBotState;

class CalendarListState extends BotState
{
    public function render(): void
    {
        $timers = Timer::query()
            ->where('telegram_user_id', schedule_bot()->userId())
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
    public function handle(): void
    {
        if (schedule_bot()->isCallbackQuery()) {
            $query = schedule_bot()->callbackQuery()->data;

            if ($query === KeyboardEnum::BACK->value) {
                keyboard()->remove();
                $newState = new MenuBotState(troute('calendar'));
                tuserstate()->changeState($newState);
                return;
            }
        } else {
            $query = schedule_bot()->message()?->getText();

            if (filter_var($query, FILTER_VALIDATE_INT)) {
                $timers = Timer::query()
                    ->where('telegram_user_id', schedule_bot()->userId())
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
                return;
            }
        }
    }
}
