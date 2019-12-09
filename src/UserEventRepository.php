<?php

declare(strict_types=1);

namespace RewardsProgram;

class UserEventRepository
{
    private array $eventStore = [];

    public function getEventsForUser(User $user): array
    {
        $userId = $user->getId();
        return array_key_exists($userId, $this->eventStore) ? $this->eventStore[$userId] : [];
    }

    public function saveEventsForUser(User $user, array $events): void
    {
        $this->eventStore[$user->getId()] = $events;
    }
}
