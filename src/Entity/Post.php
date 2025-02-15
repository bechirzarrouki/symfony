<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    
    #[ORM\Column(type: 'text')]
    private ?string $content = null;
    
    #[ORM\Column(type: 'integer')]
    private ?int $likes = 0;
    
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;
    
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
    
    public function getLikes(): ?int
    {
        return $this->likes;
    }
    
    public function setLikes(int $likes): self
    {
        $this->likes = $likes;
        return $this;
    }
    
    public function getAuthor(): ?User
    {
        return $this->author;
    }
    
    public function setAuthor(User $author): self
    {
        $this->author = $author;
        return $this;
    }
}
