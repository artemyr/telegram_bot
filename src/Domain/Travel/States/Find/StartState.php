<?php

namespace Domain\Travel\States\Find;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\Presentations\ClaimPresentation;
use Domain\Travel\States\AbstractState;

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

            $keyboard['refill'] = "Заполнить заного";
            $keyboard[KeyboardEnum::BACK->value] = KeyboardEnum::PREV->label();

            message()
                ->text($text)
                ->inlineKeyboard($keyboard)
                ->send();
        } else {
            $state = new WhereState();
            $state->render();
            tuser()->changeState($state);
        }
    }

    public function handle(): BotState
    {
        if (nutgram()->isCallbackQuery()) {
            $query = nutgram()->callbackQuery()->data;

            if ($query === KeyboardEnum::BACK->value) {
                return new MenuBotState('home');
            }

            if ($query === "refill") {
                return new WhereState();
            }
        }

        return $this;
    }
}
