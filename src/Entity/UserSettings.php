<?php

namespace App\Entity;

use App\Repository\UserSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSettingsRepository::class)]
#[ORM\Table(name: 'user_settings')]
class UserSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'settings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private int $workDuration = 1500; // 25 minutes in seconds

    #[ORM\Column]
    private int $shortBreakDuration = 300; // 5 minutes in seconds

    #[ORM\Column]
    private int $longBreakDuration = 900; // 15 minutes in seconds

    #[ORM\Column]
    private int $pomodorosUntilLongBreak = 4;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getWorkDuration(): int
    {
        return $this->workDuration;
    }

    public function setWorkDuration(int $workDuration): static
    {
        $this->workDuration = $workDuration;

        return $this;
    }

    public function getShortBreakDuration(): int
    {
        return $this->shortBreakDuration;
    }

    public function setShortBreakDuration(int $shortBreakDuration): static
    {
        $this->shortBreakDuration = $shortBreakDuration;

        return $this;
    }

    public function getLongBreakDuration(): int
    {
        return $this->longBreakDuration;
    }

    public function setLongBreakDuration(int $longBreakDuration): static
    {
        $this->longBreakDuration = $longBreakDuration;

        return $this;
    }

    public function getPomodorosUntilLongBreak(): int
    {
        return $this->pomodorosUntilLongBreak;
    }

    public function setPomodorosUntilLongBreak(int $pomodorosUntilLongBreak): static
    {
        $this->pomodorosUntilLongBreak = $pomodorosUntilLongBreak;

        return $this;
    }
}
