<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class UserCourse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    
    #[ORM\Column(type: 'boolean')]
    private ?bool $completionStatus = null;
    
    #[ORM\Column(type: 'float')]
    private ?float $score = null;
    
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;
    
    #[ORM\ManyToOne(targetEntity: Course::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getCompletionStatus(): ?bool
    {
        return $this->completionStatus;
    }
    
    public function setCompletionStatus(bool $completionStatus): self
    {
        $this->completionStatus = $completionStatus;
        return $this;
    }
    
    public function getScore(): ?float
    {
        return $this->score;
    }
    
    public function setScore(float $score): self
    {
        $this->score = $score;
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
    
    public function getCourse(): ?Course
    {
        return $this->course;
    }
    
    public function setCourse(Course $course): self
    {
        $this->course = $course;
        return $this;
    }
}
