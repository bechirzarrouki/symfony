<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'post_likes')]
    private Collection $likes;
    #[ORM\OneToMany(mappedBy: "post", targetEntity: Favorite::class, cascade: ["remove"])]
    private Collection $favorites;
public function getFavoriteCount(): int
{
    return count($this->favorites);
}

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable(); // Automatically set the creation date
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }
        return $this;
    }
    public function isFavoritedByUser(User $user): bool
    {
        foreach ($this->favorites as $favorite) {
            if ($favorite->getUser() === $user) {
                return true;
            }
        }
        return false;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // Set post to null if needed
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }
    
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(User $user): self
    {
        if (!$this->likes->contains($user)) {
            $this->likes->add($user);
        }
        return $this;
    }

    public function removeLike(User $user): self
    {
        $this->likes->removeElement($user);
        return $this;
    }

    public function isLikedByUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->likes->contains($user);
    }
}
