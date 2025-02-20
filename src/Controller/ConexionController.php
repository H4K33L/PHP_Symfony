<?php

namespace App\Controller;

use Symfony\Component\Uid\Uuid;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\LoginAuthenticator;

class ConexionController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function display(Request $request, UsersRepository $usersRepository): Response
    {
        $user = $this->getUser();

        if ($user) {
            $user = $usersRepository->find($user);
            if ($user) {
                return $this->redirectToRoute('dashboard', ['id' => $user->getId()]);
            }
        }

        return $this->render('index.html.twig');
    }

    #[Route('/connexion', name: 'app_conexion', methods: ['POST', 'GET'])]
    public function logIn(
        Request $request,
        UsersRepository $usersRepository,
        UserPasswordHasherInterface $passwordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginAuthenticator $authenticator
    ): Response {
        $error = null;
    
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $user = $usersRepository->findOneBy(['pseudo' => $data['pseudo'] ?? '']);
            dump($user);
    
            if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
                $error = 'Identifiants incorrects.';
                dump($error);
            } else {
                $userAuthenticator->authenticateUser($user, $authenticator, $request);
                return $this->redirectToRoute('dashboard', ['id' => $user->getId()]);
            }
        }

        return $this->render('index.html.twig', ['error' => $error]);
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logOut(): Response
    {
        $response = $this->redirectToRoute('home');
        $response->headers->clearCookie('user_id');
        return $response;
    }

    #[Route('/inscription', name: 'app_register', methods: ['POST', 'GET'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ): Response {
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
                $user = new Users();
                $user->setId(Uuid::v4());
                $user->setPseudo($data['pseudo']);
                $user->setEmail($data['email']);

                $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
                $user->setPassword($hashedPassword);
                $user->setScore(0);
                $user->setLastConnection(new \DateTime());

                if ($profilePicture) {
                    $fileName = uniqid() . '.' . $profilePicture->guessExtension();
                    $profilePicture->move($this->getParameter('profile_pictures_directory'), $fileName);
                    $user->setProfile_Picture($fileName);
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
                    return $this->redirectToRoute('app_conexion');
                }
            }
        }

        return $this->render('index.html.twig', [
            'error' => $error,
            'data' => $data
        ]);
    }
}
