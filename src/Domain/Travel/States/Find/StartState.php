<?php

namespace Domain\Travel\States\Find;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;

class StartState extends AbstractState
{
    public function render(): void
    {
        message()->removeLast();

        if ($this->claimExists()) {
            $text[] = "Вы уже ранее создавали поиск со следующими параметрами:";
            $claim = $this->getClaim();
            $text = array_merge($text, [
                "Где: {$claim->travelResort->title}",
                "Когда: с $claim->date_from",
                "по $claim->date_to",
                "Как: {$claim->travelFormat->title}",
            ]);
            $text[] = "Как только будут надены совпадения, они будут направлены вам";

            $keyboard[] = "Заполнить заного";
        } else {
            $text[] = "Необходимо заполнить форму";
            $keyboard[] = "Продолжить";
        }

        $keyboard[] = KeyboardEnum::BACK->label();

        message()
            ->text($text)
            ->replyKeyboard($keyboard)
            ->send();
    }

    public function handle(): BotState
    {
        $query = nutgram()->message()?->getText();

        if ($query === KeyboardEnum::BACK->label()) {
            return new MenuBotState('home');
        }

        if ($query === "Продолжить") {
            return new WhereState();
        }

        if ($query === "Заполнить заного") {
            return new WhereState();
        }

        return $this;
    }
}
