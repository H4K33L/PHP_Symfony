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
    public function display_group(UsersRepository $usersRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_conexion');
        }

        $group = $user->getGroup();
        if ($group) {
            $this->updateGroupScore($group, $entityManager);
        }

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
        $this->updateGroupScore($group, $entityManager); 

        return $this->redirectToRoute('group_show', ['id' => $group->getId()]);
    }

    #[Route('/group/{id}', name: 'group_show')]
    public function show(GroupsRepository $groupRepository, EntityManagerInterface $entityManager, string $id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_conexion');
        }
        $group = $groupRepository->find($id);
        if ($group) {
            $this->updateGroupScore($group, $entityManager);
        }

        return $this->render('groupe.html.twig', [
            'user' => $this->getUser(),
            'group' => $group,
            'owner' => $group->getOwner() ?: null,
            'members' => $group ? $group->getMembers() : []
        ]);
    }

    #[Route('/groupManager/leave', name: 'leave_group', methods: ['POST'])]
    public function leaveGroup(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        if (!$user || !$user->getGroup()) {
            return $this->redirectToRoute('groupManager');
        }
        
        $group = $user->getGroup();
        if ($group->getOwner() === $user) {
            $this->addFlash('error', 'Le propriétaire ne peut pas quitter le groupe. Vous devez le supprimer.');
            return $this->redirectToRoute('group_show', ['id' => $group->getId()]);
        }
        
        $user->setGroup(null);
        $entityManager->persist($user);
        $entityManager->flush();
        
        if ($group) {
            $this->updateGroupScore($group, $entityManager);
        }
        
        $this->addFlash('success', 'Vous avez quitté le groupe avec succès.');
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
        $this->updateGroupScore($group, $entityManager); 

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

    private function updateGroupScore(?Groups $group, EntityManagerInterface $entityManager): void
    {
        if (!$group) {
            return;
        }
    
        $score = 0;
        foreach ($group->getMembers() as $member) {
            $score += $member->getScore();
        }
    
        $group->setScore($score);
        $entityManager->persist($group);
        $entityManager->flush();
    }

    #[Route('/group/{id}/delete', name: 'group_delete', methods: ['POST'])]
    public function deleteGroup(Groups $group, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        if ($group->getOwner() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce groupe.');
        }
        
        foreach ($group->getMembers() as $member) {
            $member->setGroup(null);
            $entityManager->persist($member);
        }
        
        $entityManager->remove($group);
        $entityManager->flush();
        
        $this->addFlash('success', 'Le groupe a été supprimé avec succès.');
        return $this->redirectToRoute('groupManager');
    }
}