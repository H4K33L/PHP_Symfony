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

    #[Route('/profil/update-profile', name: 'update-profil', methods: ['POST'])]
    public function updateProfil(
        Request $request,
        UsersRepository $usersRepository,
        UserPasswordHasherInterface $passwordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): Response {
        // Récupération de l'utilisateur connecté
        /** @var Users $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupération des données du formulaire
        $data = $request->request->all();
        $profilePicture = $request->files->get('profilePicture');
        $error = null;

        if ($request->isMethod('POST')) {
            // Vérification du pseudo (s'il est déjà utilisé par un autre utilisateur)
            $userExists = $usersRepository->findOneBy(['pseudo' => $data['pseudo']]);
            if ($userExists && $userExists->getId() !== $user->getId()) {
                $error = 'Ce pseudo est déjà pris.';
            }

            // Vérification de l'email
            $emailExists = $usersRepository->findOneBy(['email' => $data['email']]);
            if (!$error && $emailExists && $emailExists->getId() !== $user->getId()) {
                $error = 'Cet email est déjà utilisé.';
            }

            // Vérification des mots de passe
            if (!$error && $data['password'] !== $data['confirmPassword']) {
                $error = 'Les mots de passe ne correspondent pas.';
            }

            if (!$error) {
                if (!empty($data['pseudo'])) {
                    $user->setPseudo($data['pseudo']);
                }
                if (!empty($data['email'])) {
                    $user->setEmail($data['email']);
                }
                if (!empty($data['password'])) {
                    $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
                    $user->setPassword($hashedPassword);
                }

                if ($profilePicture) {
                    $fileName = uniqid() . '.' . $profilePicture->guessExtension();
                    // Assurez-vous que le paramètre 'profile_pictures_directory' est défini dans vos paramètres de configuration
                    $profilePicture->move($this->getParameter('profile_pictures_directory'), $fileName);
                    $user->setProfilePicture($fileName);
                }

                // Validation de l'entité utilisateur
                $errors = $validator->validate($user);
                if (count($errors) > 0) {
                    // Récupération du premier message d'erreur
                    foreach ($errors as $e) {
                        $error = $e->getMessage();
                        break;
                    }
                } else {
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