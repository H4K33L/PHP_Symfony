<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        UsersRepository $usersRepository,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): Response {
        $data = $request->request->all();
        $profilePicture = $request->files->get('profilePicture');

        if ($data['password'] !== $data['confirmPassword']) {
            return new Response('Les mots de passe ne correspondent pas.', Response::HTTP_BAD_REQUEST);
        }

        
        $user = new Users();
        $user->setPseudo($data['pseudo']);
        $user->setEmail($data['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setScore(0);

        
        if ($profilePicture) {
            $fileName = uniqid().'.'.$profilePicture->guessExtension();
            $profilePicture->move($this->getParameter('profile_pictures_directory'), $fileName);
            $user->setProfilePicture($fileName);
        }

        
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return new Response((string) $errors, Response::HTTP_BAD_REQUEST);
        }

        
        $usersRepository->save($user, true);

        return new Response('Utilisateur créé avec succès.', Response::HTTP_CREATED);
    }
}
