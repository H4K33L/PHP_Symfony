<?php

namespace App\Entity;

use App\Repository\HabitsRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types; 
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: HabitsRepository::class)]
class Habits
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    #[ORM\Column]
    private ?int $difficulty = null;

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $start_time = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $end_time = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $completion_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadline = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\Column]
    private ?int $points = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'habits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    #[ORM\ManyToMany(targetEntity: Users::class, inversedBy: 'validatedHabits')]
    #[ORM\JoinTable(name: 'habits_validated_by_users')]
    private Collection $validatedByUsers;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->validatedByUsers = new ArrayCollection();
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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;
        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): static
    {
        if ($difficulty < 1 || $difficulty > 3) {
            throw new \InvalidArgumentException("La difficulté doit être comprise entre 1 et 3.");
        }
        $this->difficulty = $difficulty;
        $this->setColorBasedOnDifficulty();
        return $this;
    }

    private function setColorBasedOnDifficulty(): void
    {
        switch ($this->difficulty) {
            case 1:
                $this->color = 'green';
                break;
            case 2:
                $this->color = 'yellow';
                break;
            case 3:
                $this->color = 'red';
                break;
        }
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(\DateTimeInterface $start_time): static
    {
        $this->start_time = $start_time;
        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(\DateTimeInterface $end_time): static
    {
        $this->end_time = $end_time;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;
        $this->start_time = $created_at;
        $this->end_time = (clone $created_at)->modify('+1 day');
        return $this;
    }

    public function getCompletionDate(): ?\DateTimeInterface
    {
        return $this->completion_date;
    }

    public function setCompletionDate(?\DateTimeInterface $completion_date): static
    {
        $this->completion_date = $completion_date;
        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;
        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        if ($status) {
            $this->points += $this->difficulty * 10;
            $this->completion_date = new \DateTime();
        }

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;
        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getValidatedByUsers(): Collection
    {
        return $this->validatedByUsers;
    }

    public function addValidatedByUser(Users $user): static
    {
        if (!$this->validatedByUsers->contains($user)) {
            $this->validatedByUsers[] = $user;
        }
        return $this;
    }

    public function removeValidatedByUser(Users $user): static
    {
        $this->validatedByUsers->removeElement($user);
        return $this;
    }
}