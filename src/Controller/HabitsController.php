<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Habits;

class HabitsController extends AbstractController
{
    #[Route('/habitsManager', name: 'habitsManager')]
    public function display_habits_manager(EntityManagerInterface $entityManager): Response
    {
        $habits = $entityManager->getRepository(Habits::class)->findAll();

        return $this->render('habits.html.twig', [
            'habits' => $habits,
        ]);
    }

    #[Route('/addHabit', name: 'add_habit', methods: ['POST'])]
    public function addHabit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $habit = new Habits();
        $habit->setText($request->request->get('text'));
        $habit->setDifficulty($request->request->get('difficulty'));
        $habit->setColor($request->request->get('color'));
        $habit->setStartTime(new \DateTime($request->request->get('start_time')));
        $habit->setEndTime(new \DateTime($request->request->get('end_time')));
        $habit->setCreatedAt(new \DateTime());

        $entityManager->persist($habit);
        $entityManager->flush();

        return $this->redirectToRoute('habitsManager');
    }
}