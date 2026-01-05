<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $googleId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $avatarUrl = null;

    #[ORM\Column(length: 50)]
    private string $authProvider = 'local';

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(targetEntity: PomodoroSession::class, mappedBy: 'user', cascade: ['remove'])]
    private Collection $pomodoroSessions;

    #[ORM\OneToOne(targetEntity: UserSettings::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserSettings $settings = null;

    public function __construct()
    {
        $this->pomodoroSessions = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): static
    {
        $this->googleId = $googleId;

        return $this;
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

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    public function getAuthProvider(): string
    {
        return $this->authProvider;
    }

    public function setAuthProvider(string $authProvider): static
    {
        $this->authProvider = $authProvider;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, PomodoroSession>
     */
    public function getPomodoroSessions(): Collection
    {
        return $this->pomodoroSessions;
    }

    public function addPomodoroSession(PomodoroSession $pomodoroSession): static
    {
        if (!$this->pomodoroSessions->contains($pomodoroSession)) {
            $this->pomodoroSessions->add($pomodoroSession);
            $pomodoroSession->setUser($this);
        }

        return $this;
    }

    public function removePomodoroSession(PomodoroSession $pomodoroSession): static
    {
        if ($this->pomodoroSessions->removeElement($pomodoroSession)) {
            if ($pomodoroSession->getUser() === $this) {
                $pomodoroSession->setUser(null);
            }
        }

        return $this;
    }

    public function getSettings(): ?UserSettings
    {
        return $this->settings;
    }

    public function setSettings(?UserSettings $settings): static
    {
        if ($settings === null && $this->settings !== null) {
            $this->settings->setUser(null);
        }

        if ($settings !== null && $settings->getUser() !== $this) {
            $settings->setUser($this);
        }

        $this->settings = $settings;

        return $this;
    }
}
