<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Habits;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DashboardController extends AbstractController
{
    #[Route('/dashboard/{id}', name: 'user_dashboard', methods: ['GET'])]
    public function userDashboard(UsersRepository $usersRepository, string $id, EntityManagerInterface $entityManager): Response
    {
        $habits = $entityManager->getRepository(Habits::class)->findAll();
        $user = $usersRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        return $this->render('dashboard.html.twig', [
            'user' => $user,
            'habits' => $habits
        ]);
    }

    #[Route('/dashboard/toggleHabit/{id}', name: 'toggle_habit', methods: ['POST'])]
    public function toggleHabit(string $id, EntityManagerInterface $entityManager): Response
    {
        $habit = $entityManager->getRepository(Habits::class)->find($id);

        if (!$habit) {
            throw $this->createNotFoundException('Habitude non trouvée');
        }

        $habit->setStatus(!$habit->isStatus());

        $entityManager->persist($habit);
        $entityManager->flush();

        return $this->redirectToRoute('habitsManager');
    }
}
