<?php

namespace Domain\TelegramBot\Services;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Dto\ActionStateDto;
use Domain\TelegramBot\Dto\UserStateDto;
use Domain\TelegramBot\Exceptions\UserStateManagerException;
use Domain\TelegramBot\MenuBotState;
use Domain\TelegramBot\UserStateStore;
use ReflectionClass;

class UserStateManager implements UserStateContract
{
    public function get(int $userId): ?UserStateDto
    {
        return UserStateStore::get($userId);
    }

    public function load(int $userId): UserStateDto
    {
        $userDto = UserStateStore::get($userId);

        if (!$userDto) {
            $userDto = $this->make($userId, troute('home'), new MenuBotState());
            $this->write($userDto);
        }

        return $userDto;
    }

    public function write(UserStateDto $user): void
    {
        UserStateStore::set($user->userId, $user);

        logger()->debug('Write user state: ' . json_encode($user));
    }

    public function make(
        int      $userId,
        string   $path,
        BotState $state,
        string   $timezone = '',
        bool     $keyboard = false,
        array    $actions = [],
    ): UserStateDto
    {
        return new UserStateDto(
            $userId,
            $path,
            $state,
            $timezone,
            $keyboard,
            $actions,
        );
    }

    public function changePath(int $userId, string $path): void
    {
        $this->changeParam($userId, 'path', $path);
    }

    public function changeState(int $userId, BotState $state): void
    {
        $this->changeParam($userId, 'state', $state);
    }

    public function changeKeyboard(int $userId, bool $active): void
    {
        $this->changeParam($userId, 'keyboard', $active);
    }

    public function changeTimezone(int $userId, string $timezone): void
    {
        $this->changeParam($userId, 'timezone', $timezone);
    }

    public function changeAction(int $userId, ActionStateDto $action): void
    {
        $this->changeParam($userId, 'actions', [$action->code => $action]);
    }

    public function changeParam(int $userId, string $param, $value): void
    {
        $userDto = $this->load($userId);
        $fields = $userDto->toArray();
        $from = $fields[$param];

        if (is_array($value)) {
            $fields[$param] = array_merge($fields[$param], $value);
        } else {
            $fields[$param] = $value;
        }

        $newUserDto = UserStateDto::fromArray($fields);
        $this->write($newUserDto);

        if (is_object($from) || is_array($from)) {
            $from = json_encode($from);
        }

        if (is_object($value) || is_array($value)) {
            $value = json_encode($value);
        }

        logger()->debug("User $userId change param $param from $from to $value");
    }

    /**
     * @throws UserStateManagerException
     */
    protected function checkUser(UserStateDto $user): void
    {
        $r = new ReflectionClass($user);
        $a = $r->getProperties();
        foreach ($a as $property) {
            if (!$property->isInitialized()) {
                throw new UserStateManagerException('User dto crashed');
            }
        }
    }
}
