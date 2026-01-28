<?php

namespace Domain\Schedule\Tasks\States;

use Domain\Schedule\Tasks\Contracts\TaskRepositoryContract;
use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Enum\KeyboardEnum;
use Domain\TelegramBot\Exceptions\PrintableException;
use Domain\TelegramBot\MenuBotState;
use Support\Dto\RepositoryResult;

class TaskAddState extends BotState
{
    protected TaskRepositoryContract $taskRepository;

    public function __construct(protected ?string $path = null)
    {
        $this->taskRepository = app(TaskRepositoryContract::class);
        parent::__construct($path);
    }

    public function render(): void
    {
        message()
            ->text([
                "Раздел: Задачи",
                "Добавить задачу",
                "Введите название задачи в формате \"Помыть посуду 21.12.2025 17:00\"",
                "Можно вводить сразу несколько задач, каждая на новой строке",
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
                return new MenuBotState(troute('tasks'));
            }
        } else {
            $tasks = nutgram()->message()?->getText();

            $arTasks = explode("\n", $tasks);

            $response = [];

            foreach ($arTasks as $task) {
                $result = $this->taskRepository->save(nutgram()->userId(), $task);

                if ($result->state === RepositoryResult::EXISTS) {
                    $response[] = "Задача \"$task\" уже существует";
                }

                if ($result->state === RepositoryResult::RESTORED) {
                    $response[] = "Задача \"$task\" востановленна";
                }

                if ($result->state === RepositoryResult::SUCCESS_SAVED) {
                    $response[] = "Задача \"$task\" создана";
                }

                if ($result->state === RepositoryResult::ERROR) {
                    throw new PrintableException("Задача \"$task\" ошибка\n$result->message");
                }
            }

            if (!empty($response)) {
                message()
                    ->text($response)
                    ->send();
            }
        }
        return $this;
    }
}
