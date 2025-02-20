<?php

namespace App\Controller;

use Symfony\Component\Uid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GroupsRepository;
use App\Repository\UsersRepository;
use App\Entity\Invitations;
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
        $group->setId(Uuid::v4());
        $group->setName($groupName);
        $group->setOwner($user);
        $entityManager->persist($group);

        $user->setGroup($group);
        $entityManager->flush();

        return $this->redirectToRoute('group_show', ['id' => $group->getId()]);
    }

    #[Route('/group/{id}', name: 'group_show')]
    public function show(GroupsRepository $groupRepository, string $id): Response
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

    #[Route('/group/{id}/invite', name: 'group_invite', methods: ['POST'])]
    public function inviteUser(Request $request, Groups $group, UsersRepository $usersRepository, EntityManagerInterface $entityManager): Response
    {
        $pseudo = $request->request->get('pseudo');
        $receiver = $usersRepository->findOneBy(['pseudo' => $pseudo]);

        if (!$receiver) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('group_show', ['id' => $group->getId()]);
        }

        if ($receiver->getGroup()) {
            $this->addFlash('error', 'Cet utilisateur est déjà dans un groupe.');
            return $this->redirectToRoute('group_show', ['id' => $group->getId()]);
        }

        $invitation = new Invitations();
        $invitation->setId(Uuid::v4());
        $invitation->setSender($this->getUser());
        $invitation->setReceiver($receiver);
        $invitation->setGroup($group);
        $invitation->setStatus(false);
        $invitation->setSentAt(new \DateTime());

        $entityManager->persist($invitation);
        $entityManager->flush();

        $this->addFlash('success', 'Invitation envoyée.');
        return $this->redirectToRoute('group_show', ['id' => $group->getId()]);
    }

    #[Route('/invitation/{id}/accept', name: 'invitation_accept', methods: ['POST'])]
    public function acceptInvitation(Invitations $invitation, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($invitation->getReceiver() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accepter cette invitation.');
        }

        $group = $invitation->getGroup();
        $user->setGroup($group);
        $invitation->setStatus(true);

        $entityManager->remove($invitation);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez rejoint le groupe.');
        return $this->redirectToRoute('group_show', ['id' => $group->getId()]);
    }

    #[Route('/invitation/{id}/decline', name: 'invitation_decline', methods: ['POST'])]
    public function declineInvitation(Invitations $invitation, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($invitation->getReceiver() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas refuser cette invitation.');
        }

        $entityManager->remove($invitation);
        $entityManager->flush();

        $this->addFlash('success', 'Invitation refusée.');
        return $this->redirectToRoute('dashboard');
    }
}