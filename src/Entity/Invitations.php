<?php

namespace App\Entity;

use App\Repository\InvitationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvitationsRepository::class)]
class Invitations
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $invitation_id = null;

    #[ORM\Column(length: 255)]
    private ?string $sender_id = null;

    #[ORM\Column(length: 255)]
    private ?string $receiver_id = null;

    #[ORM\Column(length: 255)]
    private ?string $group_id = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $sent_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvitationId(): ?string
    {
        return $this->invitation_id;
    }

    public function setInvitationId(string $invitation_id): static
    {
        $this->invitation_id = $invitation_id;

        return $this;
    }

    public function getSenderId(): ?string
    {
        return $this->sender_id;
    }

    public function setSenderId(string $sender_id): static
    {
        $this->sender_id = $sender_id;

        return $this;
    }

    public function getReceiverId(): ?string
    {
        return $this->receiver_id;
    }

    public function setReceiverId(string $receiver_id): static
    {
        $this->receiver_id = $receiver_id;

        return $this;
    }

    public function getGroupId(): ?string
    {
        return $this->group_id;
    }

    public function setGroupId(string $group_id): static
    {
        $this->group_id = $group_id;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sent_at;
    }

    public function setSentAt(\DateTimeInterface $sent_at): static
    {
        $this->sent_at = $sent_at;

        return $this;
    }
}
