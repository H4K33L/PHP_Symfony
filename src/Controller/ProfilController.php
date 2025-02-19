<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil')]
    public function display_profil()
    {
        return $this->render('profile.html.twig');
    }


    #[Route('/profil/{id}', name: 'profil', methods: ['GET'])]
    public function userProfil(UsersRepository $usersRepository, string $id): Response
    {
        $user = $usersRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        return $this->render('profile.html.twig', [
            'user' => $user
        ]);
    }
}