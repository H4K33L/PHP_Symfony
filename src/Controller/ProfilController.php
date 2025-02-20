<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UsersRepository;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil')]
    public function display_profil(UsersRepository $usersRepository)
    {
        $user = $this->getUser();

        return $this->render('profile.html.twig', [
            'user' => $user,
        ]);
    }
}