<?php

namespace App\Entity;

use App\Repository\ReturnEntityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReturnEntityRepository::class)]
class ReturnEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $typeReturn = null;

    #[ORM\Column(type: 'float')]
    private ?float $taxRendement = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateDeadline = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: Investment::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Investment $investment = null;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTypeReturn(): ?string
    {
        return $this->typeReturn;
    }

    public function setTypeReturn(string $typeReturn): self
    {
        $this->typeReturn = $typeReturn;
        return $this;
    }

    public function getTaxRendement(): ?float
    {
        return $this->taxRendement;
    }

    public function setTaxRendement(float $taxRendement): self
    {
        $this->taxRendement = $taxRendement;
        return $this;
    }

    public function getDateDeadline(): ?\DateTimeInterface
    {
        return $this->dateDeadline;
    }

    public function setDateDeadline(\DateTimeInterface $dateDeadline): self
    {
        $this->dateDeadline = $dateDeadline;
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

    public function getInvestment(): ?Investment
    {
        return $this->investment;
    }

    public function setInvestment(Investment $investment): self
    {
        $this->investment = $investment;
        return $this;
    }
}
