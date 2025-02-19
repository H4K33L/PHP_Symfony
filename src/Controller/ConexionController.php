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

class ConexionController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function display(Request $request, UsersRepository $usersRepository): Response
    {
        $userId = $request->cookies->get('user_id');
        
        if ($userId) {
            $user = $usersRepository->find($userId);
            if ($user) {
                return $this->redirectToRoute('user_dashboard', ['id' => $user->getId()]);
            }
        }
        
        return $this->render('index.html.twig');
    }

    #[Route('/connexion', name: 'app_conexion', methods: ['POST'])]
    public function logIn(
        Request $request,
        UsersRepository $usersRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $data = $request->request->all();
        $user = $usersRepository->findOneBy(['pseudo' => $data['pseudo']]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
            return new Response('Identifiants incorrects.', Response::HTTP_UNAUTHORIZED);
        }
        
        $response = $this->redirectToRoute('user_dashboard', ['id' => $user->getId()]);
        $response->headers->setCookie(new Cookie('user_id', $user->getId(), strtotime('+7 days')));
        return $response;
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logOut(): Response
    {
        $response = $this->redirectToRoute('home');
        $response->headers->clearCookie('user_id');
        return $response;
    }

    #[Route('/inscription', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ): Response {
        $data = $request->request->all();
        $profilePicture = $request->files->get('profilePicture');

        if ($data['password'] !== $data['confirmPassword']) {
            return new Response('Les mots de passe ne correspondent pas.', Response::HTTP_BAD_REQUEST);
        }
        $user = new Users();
        $user->setId(Uuid::v4());
        $user->setPseudo($data['pseudo']);
        $user->setEmail($data['email']);

        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        $user->setScore(0);
        $user->setLastConnection(new \DateTime());
        
        if ($profilePicture) {
            $fileName = uniqid().'.'.$profilePicture->guessExtension();
            $profilePicture->move($this->getParameter('profile_pictures_directory'), $fileName);
            $user->setProfilePicture($fileName);
        }

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return new Response((string) $errors, Response::HTTP_BAD_REQUEST);
        }
        $entityManager->persist($user);
        $entityManager->flush();
        
        return new Response('Utilisateur créé avec succès.', Response::HTTP_CREATED);
    }
}
