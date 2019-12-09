<?php

declare(strict_types=1);

namespace RewardsProgram;

class User
{
    private string $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function getId(): string
    {
        return $this->userId;
    }
}