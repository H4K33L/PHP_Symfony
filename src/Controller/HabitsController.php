<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HabitsController extends AbstractController
{
    #[Route('/habitsManager', name: 'habitsManager')]
    public function display_habits_manager()
    {
        return $this->render('habits.html.twig');
    }

    /*#[Route('/habitsManager/{id}', name: 'habitsManager', methods: ['GET'])]
    public function userHabits(UsersRepository $usersRepository, string $id): Response
    {
        $user = $usersRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        return $this->render('habits.html.twig', [
            'user' => $user
        ]);
    }*/
}