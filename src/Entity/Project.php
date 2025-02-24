<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = null;
    
    #[ORM\Column(type: 'text')]
    private ?string $description = null;
    
    #[ORM\Column(type: 'float')]
    private ?float $budget = null;
    
    #[ORM\Column(type: 'string', length: 100)]
    private ?string $status = null;
    
    // Owner of the project: a User with role "ROLE_COMPANY"
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;
    
    // Updated mapping: Enable orphanRemoval so that when a requirement is removed from this project,
    // Doctrine will automatically delete it.
    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Requirement::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $requirements;
    
    public function __construct()
    {
        $this->requirements = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getTitle(): ?string
    {
        return $this->title;
    }
    
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
    
    public function getBudget(): ?float
    {
        return $this->budget;
    }
    
    public function setBudget(float $budget): self
    {
        $this->budget = $budget;
        return $this;
    }
    
    public function getStatus(): ?string
    {
        return $this->status;
    }
    
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
    
    public function getOwner(): ?User
    {
        return $this->owner;
    }
    
    public function setOwner(User $owner): self
    {
        $this->owner = $owner;
        return $this;
    }
    
    /**
     * @return Collection|Requirement[]
     */
    public function getRequirements(): Collection
    {
        return $this->requirements;
    }
    
    public function addRequirement(Requirement $requirement): self
    {
        if (!$this->requirements->contains($requirement)) {
            $this->requirements[] = $requirement;
            $requirement->setProject($this);
        }
        return $this;
    }
    
    // Updated removeRequirement(): We no longer set the requirement's project to null.
    public function removeRequirement(Requirement $requirement): self
    {
        $this->requirements->removeElement($requirement);
        return $this;
    }
}
