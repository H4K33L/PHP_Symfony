<?php

namespace App\Service;

use App\Entity\Habits;
use Doctrine\ORM\EntityManagerInterface;

class HabitResetService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function resetDailyHabits(): void
    {
        $habits = $this->entityManager->getRepository(Habits::class)->findBy(['text' => ['Faire du sport', 'Lire un livre']]);
    
        foreach ($habits as $habit) {
            if (!$habit->isStatus()) {
                $user = $this->entityManager->getRepository(Users::class)->find($habit->getUserId());
                if ($user) {
                    $pointsToDeduct = $habit->getDifficulty() * 10;
                    $user->setScore($user->getScore() - $pointsToDeduct);
                    $this->entityManager->persist($user);
                }
            }
            $habit->setStatus(false);
            $habit->setCreatedAt(new \DateTime());
            $this->entityManager->persist($habit);
        }
    
        $this->entityManager->flush();
    }
}