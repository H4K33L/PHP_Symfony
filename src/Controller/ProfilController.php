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

    #[Route('/update-profile/{id}', name: 'update-profil', methods: ['POST'])]
    public function updateProfil(ValidatorInterface $validator,Request $request, UserPasswordHasherInterface $passwordHasher, string $id, EntityManagerInterface $entityManager,  UsersRepository $usersRepository): Response {
        $user = $entityManager->getRepository(Users::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('utilisateur non trouvée');
        }

        $data = $request->request->all();
        $profilePicture = $request->files->get('profilePicture');
        $error = null;

        if ($request->isMethod('POST')) {
            $userExists = $usersRepository->findOneBy(['pseudo' => $data['pseudo']]);
            if ($userExists && $userExists->getId() !== $user->getId()) {
                $error = 'Ce pseudo est déjà pris.';
            }

            $emailExists = $usersRepository->findOneBy(['email' => $data['email']]);
            if (!$error && $emailExists && $emailExists->getId() !== $user->getId()) {
                $error = 'Cet email est déjà utilisé.';
            }

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

    #[Route('/deleteuser/{id}', name: 'delete_user', methods: ['POST'])]
    public function deleteHabit(string $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(Users::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('utilisateur non trouvée');
        }

        $groupId = $user->getGroupId();

        if ($groupId) {
            $usersInSameGroup = $entityManager->getRepository(Users::class)->findBy(['group_id' => $groupId]);
            foreach ($usersInSameGroup as $groupMember) {
                if ($groupMember->getId() !== $user->getId()) {
                    $groupMember->setGroupId(null);
                    $entityManager->persist($groupMember);
                }
            }
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }
    #[Route('/deleteuser/{id}', name: 'delete_user', methods: ['POST'])]
public function deleteUser(string $id, EntityManagerInterface $entityManager): Response
{
    $user = $entityManager->getRepository(Users::class)->find($id);

    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    // Supprimer l'utilisateur
    $entityManager->remove($user);
    $entityManager->flush();

    return $this->redirectToRoute('home');
}
}