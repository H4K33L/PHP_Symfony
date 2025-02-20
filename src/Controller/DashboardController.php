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

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(Request $request, UsersRepository $usersRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_conexion');
        }

        $user = $usersRepository->find($user);
        if (!$user) {
            return $this->redirectToRoute('app_conexion');
        }

        return $this->render('dashboard.html.twig', ['user' => $user]);
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
