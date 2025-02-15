<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Investment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    
    #[ORM\Column(type: 'float')]
    private ?float $amount = null;
    
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $type = null;
    
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null; // A user with role "ROLE_INVESTOR"
    
    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getAmount(): ?float
    {
        return $this->amount;
    }
    
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }
    
    public function getType(): ?string
    {
        return $this->type;
    }
    
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }
    
    public function getUser(): ?User
    {
        return $this->user;
    }
    
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }
    
    public function getProject(): ?Project
    {
        return $this->project;
    }
    
    public function setProject(Project $project): self
    {
        $this->project = $project;
        return $this;
    }
}
