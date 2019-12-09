<?php

declare(strict_types=1);

namespace RewardsProgram;

use Symfony\Component\EventDispatcher\EventDispatcher;

class IncentiveListener
{
    private EventDispatcher $eventDispatcher;

    private RewardRepository $rewardRepository;

    private UserEventRepository $userEventRepository;

    public function __construct(EventDispatcher $eventDispatcher, RewardRepository $rewardRepository, UserEventRepository $userEventRepository)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->rewardRepository = $rewardRepository;
        $this->userEventRepository = $userEventRepository;

        foreach(self::getEventListeners() as $eventName => $listener){
            $this->eventDispatcher->addListener($eventName, $listener);
        }
    }

    private function hasPreviousContiguousDays(int $days, array $dates): bool
    {
        $totalSeconds = 0;
        $current = array_pop($dates);
        while($previous = array_pop($dates)){
            $diffSeconds = $current->getTimestamp() - $previous->getTimestamp() ;
            if($diffSeconds > 86400){
                break;
            }
            $totalSeconds += $diffSeconds;
            $current = $previous;
        }
        return $totalSeconds/86400 >= $days - 1;
    }

    private function getEventListeners(): array
    {
        return [
            UserBirthEvent::NAME => function(UserBirthEvent $event){
                $this->rewardRepository->createRewardForBirthEvent($event->getUser());
            },
            UserLogDataEvent::NAME  => function(UserLogDataEvent $event){
                $logDataEvents = $this->userEventRepository->getEventsForUser($event->getUser());
                $logDataEvents[] = $event->getDate();

                if($this->hasPreviousContiguousDays(5, $logDataEvents)){
                    $this->rewardRepository->createRewardForConcurrentDaysLogged($event->getUser());
                    $logDataEvents = [];
                }
                $this->userEventRepository->saveEventsForUser($event->getUser(), $logDataEvents);
            }
        ];
    }
}
