<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Quiz;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    private ?int $number = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $profileImage = null;

    #[ORM\Column(length: 255)]
    private ?string $roles = 'ROLE_USER'; // Default role
    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private ?bool $banned = false;

    #[ORM\ManyToMany(targetEntity: Quiz::class, mappedBy: 'participants')]
    private Collection $quizzesTaken;

    public function __construct()
    {
        $this->quizzesTaken = new ArrayCollection();
    }
    public function getQuizzesTaken(): Collection
    {
        return $this->quizzesTaken;
    }

    public function addQuizTaken(Quiz $quiz): self
    {
        if (!$this->quizzesTaken->contains($quiz)) {
            $this->quizzesTaken->add($quiz);
            $quiz->addParticipant($this); // Ensures bidirectional consistency
        }

        return $this;
    }

    public function removeQuizTaken(Quiz $quiz): self
    {
        if ($this->quizzesTaken->removeElement($quiz)) {
            $quiz->getParticipants()->removeElement($this);
        }

        return $this;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
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



    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;
        return $this;
    }

    public function getRoles(): array
    {
        // Convert single role to array format as required by UserInterface
        return [$this->roles];
    }
    public function isBanned(): ?bool
    {
        return $this->banned;
    }
    public function setBanned(bool $banned): static
    {
        $this->banned = $banned;
        return $this;
    }

    public function setRoles(?string $role): self
    {
        $this->roles = $role ?? 'ROLE_USER';
        return $this;
    }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): static
    {
        $this->profileImage = $profileImage;
        return $this;
    }
}
