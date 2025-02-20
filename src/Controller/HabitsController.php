<?php

namespace App\Controller;

use Symfony\Component\Uid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Habits;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
    public function addHabit(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        $token = $tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;

        if (!$user || !is_object($user)) {
            return $this->redirectToRoute('app_login');
        }

        $habit = new Habits();
        

        $habit->setHabitId(Uuid::v4());
        $habit->setUserId($user->getId()); 
        $habit->setText($request->request->get('text'));
        $habit->setDifficulty((int) $request->request->get('difficulty'));
        $habit->setColor($request->request->get('color'));
        $habit->setStartTime(new \DateTime());
        $habit->setEndTime((new \DateTime())->modify('+7 days'));
        $habit->setCreatedAt(new \DateTime());
        $habit->setStatus(false);
        $habit->setPoints(0);

        $entityManager->persist($habit);
        $entityManager->flush();

        return $this->redirectToRoute('habitsManager');
    }

    #[Route('/deleteHabit/{id}', name: 'delete_habit', methods: ['POST'])]
    public function deleteHabit(string $id, EntityManagerInterface $entityManager): Response
    {
        $habit = $entityManager->getRepository(Habits::class)->find($id);

        if (!$habit) {
            throw $this->createNotFoundException('Habitude non trouvée');
        }

        $entityManager->remove($habit);
        $entityManager->flush();

        return $this->redirectToRoute('habitsManager');
    }

    #[Route('/toggleHabit/{id}', name: 'toggle_habit', methods: ['POST'])]
public function toggleHabit(string $id, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
{
    $token = $tokenStorage->getToken();
    $user = $token ? $token->getUser() : null;

    if (!$user || !is_object($user)) {
        return $this->redirectToRoute('app_login');
    }

    $habit = $entityManager->getRepository(Habits::class)->find($id);

    if (!$habit) {
        throw $this->createNotFoundException('Habitude non trouvée');
    }

    if ($habit->isStatus()) {
        $habit->setStatus(false);
        $this->updateUserPoints($user, -$habit->getDifficulty() * 5, $entityManager);
    } else {
        $habit->setStatus(true);
        $this->updateUserPoints($user, $habit->getDifficulty() * 5, $entityManager);
    }

    $entityManager->persist($habit);
    $entityManager->flush();

    return $this->redirectToRoute('habitsManager');
}

    private function updateUserPoints($user, int $points, EntityManagerInterface $entityManager)
{
    if (!$user) {
        return;
    }

    $user->setScore($user->getScore() + $points);
    $entityManager->persist($user);
    $entityManager->flush();
}
}