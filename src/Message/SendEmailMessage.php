<?php

namespace App\Message;

final class SendEmailMessage
{
    private $command;
    private $bool;
    private $user;
    private $mode;

    public function __construct(
        int $mode,
        int $user,
        int $command,
        int $bool
    ) {
        $this->mode = $mode;
        $this->user = $user;
        $this->command = $command;
        $this->bool = $bool;
    }

    public function getMode(): int
    {
        return $this->mode;
    }

    public function getUser(): int
    {
        return $this->user;
    }

    public function getCommand(): int
    {
        return $this->command;
    }

    public function getBool(): int
    {
        return $this->bool;
    }
}
