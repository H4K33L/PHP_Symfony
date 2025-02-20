<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GroupsRepository;
use App\Repository\UsersRepository;
use App\Entity\Groups;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GroupController extends AbstractController
{
    #[Route('/groupManager', name: 'groupManager')]
    public function display_group(UsersRepository $usersRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_conexion');
        }

        $group = $user->getGroup();

        return $this->render('groupe.html.twig', [
            'user' => $user,
            'group' => $group
        ]);
    }

    #[Route('/groupManager/create', name: 'group_create', methods: ['GET', 'POST'])]
    public function createGroup(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user || $user->getGroup()) {
            return $this->redirectToRoute('groupManager');
        }

        $groupName = $request->request->get('group_name');

        if (!$groupName) {
            return $this->redirectToRoute('groupManager');
        }

        $group = new Groups();
        $group->setName($groupName);
        $group->setScore(0);
        $group->addUser($user);
        $entityManager->persist($group);

        $user->setGroup($group);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('groupManager');
    }

    #[Route('/groupManager/leave', name: 'leave_group', methods: ['GET', 'POST'])]
    public function leaveGroup(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user || !$user->getGroup()) {
            return $this->redirectToRoute('groupManager');
        }

        $user->setGroup(null);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('groupManager');
    }

    /*#[Route('/groupManager/{id}', name: 'group', methods: ['GET'])]
    public function userPoints(UsersRepository $usersRepository, string $id): Response
    {
        $user = $usersRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        return $this->render('groupe.html.twig', [
            'user' => $user
        ]);
    }*/
}