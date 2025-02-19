<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function display_dashboard(): Response
    {
        return $this->render('dashboard.html.twig');
    }

    #[Route('/dashboard/{id}', name: 'user_dashboard', methods: ['GET'])]
    public function userDashboard(UsersRepository $usersRepository, int $id): Response
    {
        $user = $usersRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        return $this->render('dashboard.html.twig', [
            'user' => $user
        ]);
    }

}
