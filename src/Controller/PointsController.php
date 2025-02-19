<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PointsController extends AbstractController
{
    #[Route('/points', name: 'points')]
    public function display_points()
    {
        return $this->render('point.html.twig');
    }

    #[Route('/points/{id}', name: 'points', methods: ['GET'])]
    public function userPoints(UsersRepository $usersRepository, string $id): Response
    {
        $user = $usersRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        return $this->render('point.html.twig', [
            'user' => $user
        ]);
    }
}