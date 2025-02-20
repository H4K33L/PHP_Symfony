<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\LoginAuthenticator;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil')]
    public function display_profil(Request $request, UsersRepository $usersRepository)
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_conexion');
        }

        $user = $usersRepository->find($user);
        if (!$user) {
            return $this->redirectToRoute('app_conexion');
        }

        return $this->render('profile.html.twig', ['user' => $user]);
    }

    #[Route('/profil/update-profile', name: 'update-profil', methods: ['POST', 'GET'])]
    public function update_profil(
        Request $request,
        UsersRepository $usersRepository,
        UserPasswordHasherInterface $passwordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginAuthenticator $authenticator
    ): Response { 

        $user = $this->getUser();

        $data = $request->request->all();
        $profilePicture = $request->files->get('profilePicture');
        $error = null;

        if ($request->isMethod('POST')) {
            $userExists = $entityManager->getRepository(Users::class)->findOneBy(['pseudo' => $data['pseudo']]);
            if ($userExists) {
                $error = 'Ce pseudo est déjà pris.';
            }

            $emailExists = $entityManager->getRepository(Users::class)->findOneBy(['email' => $data['email']]);
            if (!$error && $emailExists) {
                $error = 'Cet email est déjà utilisé.';
            }

            if (!$error && $data['password'] !== $data['confirmPassword']) {
                $error = 'Les mots de passe ne correspondent pas.';
            }

            if (!$error) {
                if ($data['pseudo'] !== "") {
                    $user->setPseudo($data['pseudo']);
                }
                if ($data['email'] !== "") {
                    $user->setEmail($data['email']);
                }
                if ($data['password'] !== ""){
                    $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
                    $user->setPassword($hashedPassword);
                }

                if ($profilePicture) {
                    $fileName = uniqid() . '.' . $profilePicture->guessExtension();
                    $profilePicture->move($this->getParameter('profile_pictures_directory'), $fileName);
                    $user->setProfilePicture($fileName);
                }

                $errors = $validator->validate($user);
                if (count($errors) > 0) {
                    foreach ($errors as $e) {
                        $error = $e->getMessage();
                        break;
                    }
                } else {
                    $entityManager->persist($user);
                    $entityManager->flush();
                    return $this->render('profile.html.twig', ['user' => $user]);
                }
            }
        }

        return $this->render('index.html.twig', [
            'error' => $error,
            'data' => $data
        ]);
    }
}