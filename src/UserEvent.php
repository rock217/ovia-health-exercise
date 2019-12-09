<?php

declare(strict_types=1);

namespace RewardsProgram;

use Symfony\Contracts\EventDispatcher\Event;

use \DateTimeImmutable;

abstract class UserEvent extends Event
{
    private User $user;
    private DateTimeImmutable $eventDate;

    public function __construct(User $user, DateTimeImmutable $eventDate)
    {
        $this->user = $user;
        $this->eventDate = $eventDate;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->eventDate;
    }
}
