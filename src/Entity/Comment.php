<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    
    #[ORM\Column(type: 'text')]
    private ?string $content = null;
    
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date_creation = null;
    
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date_modification = null;
    
    #[ORM\ManyToOne(targetEntity: Post::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;
    
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
    
    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }
    
    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;
        return $this;
    }
    
    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->date_modification;
    }
    
    public function setDateModification(\DateTimeInterface $date_modification): self
    {
        $this->date_modification = $date_modification;
        return $this;
    }
    
    public function getPost(): ?Post
    {
        return $this->post;
    }
    
    public function setPost(Post $post): self
    {
        $this->post = $post;
        return $this;
    }
}
