<?php

namespace App\Entity;

use App\Repository\PointsLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PointsLogRepository::class)]
class PointsLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'user')]
    private ?Groups $relation = null;

    #[ORM\ManyToOne(inversedBy: 'pointsLogs')]
    private ?Groups $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRelation(): ?Groups
    {
        return $this->relation;
    }

    public function setRelation(?Groups $relation): static
    {
        $this->relation = $relation;

        return $this;
    }

    public function getUser(): ?Groups
    {
        return $this->user;
    }

    public function setUser(?Groups $user): static
    {
        $this->user = $user;

        return $this;
    }
}
