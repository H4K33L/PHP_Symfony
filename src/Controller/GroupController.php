<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    #[Route('/groupManager', name: 'groupManager')]
    public function display_group()
    {
        return $this->render('groupe.html.twig');
    }

    #[Route('/groupManager/{id}', name: 'group', methods: ['GET'])]
    public function userPoints(UsersRepository $usersRepository, string $id): Response
    {
        $user = $usersRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        return $this->render('groupe.html.twig', [
            'user' => $user
        ]);
    }
}