<?php

namespace App\Entity;

use App\Repository\GroupsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Users;

#[ORM\Entity(repositoryClass: GroupsRepository::class)]
class Groups
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(length: 255)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $score = null;

    /**
     * @var Collection<int, PointsLog>
     */
    #[ORM\OneToMany(targetEntity: Users::class, mappedBy: 'group')]
    private Collection $users;

    /**
     * @var Collection<int, PointsLog>
     */
    #[ORM\OneToMany(targetEntity: PointsLog::class, mappedBy: 'user')]
    private Collection $pointsLogs;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->pointsLogs = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(Users $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setGroup($this);
        }

        return $this;
    }

    public function removeUser(Users $user): static
    {
        if ($this->users->removeElement($user)) {
            if ($user->getGroup() === $this) {
                $user->setGroup(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, PointsLog>
     */
    public function getPointsLogs(): Collection
    {
        return $this->pointsLogs;
    }

    public function addPointsLog(PointsLog $pointsLog): static
    {
        if (!$this->pointsLogs->contains($pointsLog)) {
            $this->pointsLogs->add($pointsLog);
            $pointsLog->setUser($this);
        }

        return $this;
    }

    public function removePointsLog(PointsLog $pointsLog): static
    {
        if ($this->pointsLogs->removeElement($pointsLog)) {
            // set the owning side to null (unless already changed)
            if ($pointsLog->getUser() === $this) {
                $pointsLog->setUser(null);
            }
        }

        return $this;
    }
}