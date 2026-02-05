<?php

namespace Domain\Travel\States\Questionnaire;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\States\AbstractState;
use Domain\Travel\States\Find\WhereState;

class NameState extends AbstractState
{
    public function render(): void
    {
        $keyboard[] = nutgram()->user()->first_name;
        $keyboard[] = 'Не указывать';
        $keyboard[] = KeyboardEnum::BACK->label();

        message()
            ->text([
                "Укажите ваше имя",
            ])
            ->replyKeyboard($keyboard)
            ->send();
    }

    public function handle(): BotState
    {
        $claim = $this->getClaim();

        if (empty($claim)) {
            message('Ваша заявка потеряна. Начните заного');
            return new WhereState();
        }

        $query = nutgram()->message()?->getText();

        if ($query === KeyboardEnum::BACK->label()) {
            return new MenuBotState(troute('home'));
        }

        if ($query === "Не указывать") {
            return new MenuBotState(troute('home'));
        }

        if (!empty($query)) {
            if (empty($claim->travelQuestionnaire)) {
                $questionnaire = $claim->travelQuestionnaire()->create([
                    'telegram_user_id' => nutgram()->userId(),
                    'name' => $query,
                ]);
                $claim->travel_questionnaire_id = $questionnaire->id;
                $claim->save();
            } else {
                $questionnaire = $claim->travelQuestionnaire;
                $questionnaire->name = $query;
                $questionnaire->save();
            }
        }

        return $this;
    }
}
