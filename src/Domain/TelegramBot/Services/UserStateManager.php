<?php

namespace Domain\TelegramBot\Services;

use Domain\TelegramBot\BotState;
use Domain\TelegramBot\Contracts\UserStateContract;
use Domain\TelegramBot\Dto\UserStateDto;
use Domain\TelegramBot\Exceptions\UserStateManagerException;
use Domain\TelegramBot\UserStateStore;
use ReflectionClass;

class UserStateManager implements UserStateContract
{
    /**
     * @throws UserStateManagerException
     */
    public function get(int $userId): ?UserStateDto
    {
        $userDto = UserStateStore::get($userId);

        if ($userDto) {
            $this->checkUser($userDto);
        }

        return $userDto;
    }

    /**
     * @throws UserStateManagerException
     */
    public function write(UserStateDto $user): void
    {
        $this->checkUser($user);

        UserStateStore::set($user->userId, $user);

        logger()->debug('Write user state: ' . json_encode($user));
    }

    public function make(
        int      $userId,
        BotState $state,
        int      $chatId = null,
        string   $timezone = '',
        bool     $keyboard = false,
    ): UserStateDto
    {
        return new UserStateDto(
            $userId,
            $chatId,
            $state,
            $timezone ?? '',
            $keyboard,
        );
    }

    /**
     * @throws UserStateManagerException
     */
    public function changeState(int $userId, BotState $state): void
    {
        $this->changeParam($userId, 'state', $state);
    }

    /**
     * @throws UserStateManagerException
     */
    public function changeKeyboard(int $userId, bool $active): void
    {
        $this->changeParam($userId, 'keyboard', $active);
    }

    /**
     * @throws UserStateManagerException
     */
    public function changeTimezone(int $userId, string $timezone): void
    {
        $this->changeParam($userId, 'timezone', $timezone);
    }

    /**
     * @throws UserStateManagerException
     */
    public function changeParam(int $userId, string $param, $value): void
    {
        $userDto = $this->get($userId);
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
            if (!$property->isInitialized($user)) {
                UserStateStore::forget($user->userId);
                throw new UserStateManagerException('User dto crashed');
            }
        }
    }
}
