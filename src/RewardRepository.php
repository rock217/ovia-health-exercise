<?php

declare(strict_types=1);

namespace RewardsProgram;

class RewardRepository
{
    public function createRewardForBirthEvent(User $user): void
    {
        // Save something indicating a reward should be sent to $user.
    }

    public function createRewardForConcurrentDaysLogged(User $user): void
    {
        // Save something indicating a reward should be sent to $user.
    }
}
