<?php
// src/Entity/Favorite.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Investment;

#[ORM\Entity]
#[ORM\Table(name: "InvestmentFavorite")]
class InvestmentFavorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ["persist"])]  // Added cascade persist here
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Investment::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Investment $post = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getPost(): ?Investment
    {
        return $this->post;
    }

    public function setPost(?Investment $post): self
    {
        $this->post = $post;
        return $this;
    }
}
