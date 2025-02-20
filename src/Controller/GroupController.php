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
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
            'group' => $group,
        ]);
    }

    #[Route('/groupManager/create', name: 'group_create', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function createGroup(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($user->getOwnedGroup() !== null) {
            $this->addFlash('danger', 'Vous possédez déjà un groupe.');
            return $this->redirectToRoute('home');
        }

        $groupName = $request->request->get('group_name');

        if (!$groupName) {
            return $this->redirectToRoute('groupManager');
        }

        $group = new Groups();
        $group->setName($groupName);
        $group->setOwner($user);
        $entityManager->persist($group);

        $user->setGroup($group);
        $entityManager->flush();

        return $this->redirectToRoute('group_show', ['id' => $group->getId()]);
    }

    #[Route('/group/{id}', name: 'group_show')]
    public function show(GroupsRepository $groupRepository, int $id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_conexion');
        }
        $group = $groupRepository->find($id);

        return $this->render('groupe.html.twig', [
            'user' => $this->getUser(),
            'group' => $group,
            'owner' => $group->getOwner() ?: null,
            'members' => $group ? $group->getMembers() : []
        ]);
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
}