<?php

namespace Domain\Travel\States\Find;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\Presentations\ClaimPresentation;

class StartState extends AbstractState
{
    public function render(): void
    {
        message()->removeLast();

        if ($this->claimExists()) {
            $text[] = "Вы уже ранее создавали поиск со следующими параметрами:";
            $claim = $this->getClaim();
            $text[] = ClaimPresentation::make($claim)->textMessage();
            $text[] = "Как только будут надены совпадения, они будут направлены вам";

            $keyboard[] = "Заполнить заного";
            $keyboard[] = KeyboardEnum::BACK->label();

            message()
                ->text($text)
                ->replyKeyboard($keyboard)
                ->send();
        } else {
            $state = new WhereState();
            $state->render();
            tuser()->changeState($state);
        }
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
