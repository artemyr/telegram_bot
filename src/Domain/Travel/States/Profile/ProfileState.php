<?php

namespace Domain\Travel\States\Profile;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\MenuBotState;
use Domain\Travel\Presentations\ClaimPresentation;
use Domain\Travel\Presentations\QuestionnairePresentation;
use Domain\Travel\States\AbstractState;

class ProfileState extends AbstractState
{
    public function render(): void
    {
        message()
            ->text([
                "Ваша анкета",
                "\n",
                ClaimPresentation::make($this->getClaim())->textMessage(),
                "\n",
                QuestionnairePresentation::make($this->getQuestionnaire())->textMessage(),
            ])
            ->inlineKeyboard(keyboard()->back())
            ->send();
    }

    public function handle(): BotState
    {
        $query = nutgram()->callbackQuery()->data;

        if ($query === KeyboardEnum::BACK->value) {
            return new MenuBotState(troute('home'));
        }

        return $this;
    }
}
