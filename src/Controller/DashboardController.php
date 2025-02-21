<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use App\Repository\HabitsRepository;
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
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(Request $request, UsersRepository $usersRepository, HabitsRepository $habitsRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_conexion');
        }
        
        $user = $usersRepository->find($user);
        if (!$user) {
            return $this->redirectToRoute('app_conexion');
        }
        
        $dailyHabits = $habitsRepository->findBy([
            'text' => ['Faire du sport', 'Lire un livre']
        ]);

        return $this->render('dashboard.html.twig', [
            'user' => $user,
            'dailyHabits' => $dailyHabits
        ]);
    }

    #[Route('/dashboard/{id}', name: 'user_dashboard', methods: ['GET'])]
    public function userDashboard(UsersRepository $usersRepository, HabitsRepository $habitsRepository, string $id): Response
    {
        $habits = $entityManager->getRepository(Habits::class)->findAll();
        $user = $usersRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $dailyHabits = $habitsRepository->findBy([
            'text' => ['Faire du sport', 'Lire un livre']
        ]);

        return $this->render('dashboard.html.twig', [
            'user' => $user,
            'dailyHabits' => $dailyHabits
        ]);
    }
}