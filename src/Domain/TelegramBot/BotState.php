<?php

namespace Domain\TelegramBot;

abstract class BotState
{
    public function __construct(
        protected ?string $path = null
    )
    {
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    abstract public function render(): void;

    public function handle(): void
    {
    }

    protected function transition(BotState $state): BotState
    {
        tuserstate()->changeState($state);
        return $state;
    }

    protected function exit(): void
    {
        tuserstate()->changeState(new MenuBotState('home'));
    }

    protected function save(): void
    {
        $newState = new static($this->path);
        tuserstate()->changeState($newState);
    }
}
