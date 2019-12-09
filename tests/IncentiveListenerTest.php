<?php

declare(strict_types=1);

namespace RewardsProgramTest;

use PHPUnit\Framework\TestCase;

use \DateInterval;
use \DateTimeImmutable;

use RewardsProgram\IncentiveListener;
use RewardsProgram\UserLogDataEvent;
use RewardsProgram\UserBirthEvent;
use RewardsProgram\RewardRepository;
use RewardsProgram\User;
use RewardsProgram\UserEventRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;

class IncentiveListenerTest extends TestCase
{
    public function testBirthEventReward()
    {
        $user = new User("test123");

        $rewardRepository = self::createMock(RewardRepository::class);

        $rewardRepository->expects(self::once())
            ->method('createRewardForBirthEvent')
            ->with($user);

        $eventDispatcher = new EventDispatcher();

        new IncentiveListener($eventDispatcher, $rewardRepository, new UserEventRepository());

        $eventDispatcher->dispatch(
            new UserBirthEvent($user, new DateTimeImmutable()),
            UserBirthEvent::NAME
        );
    }

    public function testFiveConcurrentLogDataReward()
    {
        $user = new User("test123");

        $rewardRepository = self::createMock(RewardRepository::class);

        $rewardRepository->expects(self::once())
            ->method('createRewardForConcurrentDaysLogged')
            ->with($user);

        $eventDispatcher = new EventDispatcher();

        new IncentiveListener($eventDispatcher, $rewardRepository, new UserEventRepository());

        $oneDayInterval = DateInterval::createFromDateString('1 day');

        $eventDate = (new DateTimeImmutable("5 days ago"));

        foreach (range(1, 5) as $i) {
            $eventDispatcher->dispatch(
                new UserLogDataEvent($user, $eventDate),
                UserLogDataEvent::NAME
            );
            $eventDate = $eventDate->add($oneDayInterval);
        }
    }

    public function testDayGapNoReward()
    {
        $user = new User("test123");

        $rewardRepository = self::createMock(RewardRepository::class);

        $rewardRepository->expects(self::never())
            ->method('createRewardForConcurrentDaysLogged')
            ->with($user);

        $eventDispatcher = new EventDispatcher();

        new IncentiveListener($eventDispatcher, $rewardRepository, new UserEventRepository());

        $now = (new DateTimeImmutable());
        foreach ([ 6, 5, 4, 2, 1 ] as $daysAgo) {
            $date = $now->sub(DateInterval::createFromDateString("$daysAgo days"));
            $eventDispatcher->dispatch(
                new UserLogDataEvent($user, $date),
                UserLogDataEvent::NAME
            );
        }
    }
}