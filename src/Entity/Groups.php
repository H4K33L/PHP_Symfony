<?php

namespace App\Entity;

use App\Repository\GroupsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupsRepository::class)]
class Groups
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string  $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $score = null;

    /**
     * @var Collection<int, PointsLog>
     */
    #[ORM\OneToMany(targetEntity: PointsLog::class, mappedBy: 'relation')]
    private Collection $user;

    /**
     * @var Collection<int, PointsLog>
     */
    #[ORM\OneToMany(targetEntity: PointsLog::class, mappedBy: 'user')]
    private Collection $pointsLogs;

    public function __construct()
    {
        $this->user = new ArrayCollection();
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
     * @return Collection<int, PointsLog>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(PointsLog $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
            $user->setRelation($this);
        }

        return $this;
    }

    public function removeUser(PointsLog $user): static
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRelation() === $this) {
                $user->setRelation(null);
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
