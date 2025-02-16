<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity]
#[ORM\Table(name: 'investment')]
class Investment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $content = null;

    #[ORM\Column(type: 'json')]
    private array $investmentTypes = [];

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'investments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return array
     */
    public function getInvestmentTypes(): array
    {
        return $this->investmentTypes;
    }

    /**
     * @param array $investmentTypes
     * @return self
     */
    public function setInvestmentTypes(array $investmentTypes): self
    {
        $this->investmentTypes = $investmentTypes;

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
}
