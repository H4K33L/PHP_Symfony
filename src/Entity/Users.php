<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $id = null;
    
    #[ORM\Column(length: 255, unique: true)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profile_picture = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $last_connection = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $score = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\OneToMany(targetEntity: Invitations::class, mappedBy: 'receiver')]
    private ?Collection $receivedInvitations = null;


    #[ORM\OneToOne(mappedBy: 'owner', targetEntity: Groups::class, cascade: ['persist', 'remove'])]
    private ?Groups $ownedGroup = null;

    #[ORM\ManyToOne(targetEntity: Groups::class, inversedBy: 'members')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Groups $group = null;


    public function __construct()
    {
        $this->receivedInvitations = new ArrayCollection();
    }

    public function getReceivedInvitations(): Collection
    {
        return $this->receivedInvitations;
    }

    public function addReceivedInvitation(Invitations $invitation): self
    {
        if (!$this->receivedInvitations->contains($invitation)) {
            $this->receivedInvitations[] = $invitation;
            $invitation->setReceiver($this);
        }
        return $this;
    }

    public function removeReceivedInvitation(Invitations $invitation): self
    {
        if ($this->receivedInvitations->removeElement($invitation)) {
            if ($invitation->getReceiver() === $this) {
                $invitation->setReceiver(null);
            }
        }
        return $this;
    }

    public function getGroup(): ?Groups
    {
        return $this->group;
    }
    
    public function setGroup(?Groups $group): static
    {
        $this->group = $group;
        return $this;
    }

    public function getOwnedGroup(): ?Groups
    {
        return $this->ownedGroup;
    }

    public function setOwnedGroup(?Groups $ownedGroup): static
    {
        $this->ownedGroup = $ownedGroup;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }
    
    public function setId(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profile_picture;
    }

    public function setProfilePicture(?string $profile_picture): static
    {
        $this->profile_picture = $profile_picture;
        return $this;
    }

    public function getLastConnection(): ?\DateTimeInterface
    {
        return $this->last_connection;
    }

    public function setLastConnection(?\DateTimeInterface $last_connection): static
    {
        $this->last_connection = $last_connection;
        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;
        return $this;
    }

    public function getRoles(): array
    {
        return array_unique(array_merge($this->roles, ['ROLE_USER']));
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }
}
