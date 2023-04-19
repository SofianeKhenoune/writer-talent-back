<?php

namespace App\Entity;

use App\Repository\PostRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_post"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Groups({"get_post"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"get_post"})
     */
    private $content;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"get_post"})
     */
    private $nbViews;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"get_post"})
     */
    private $nbLikes;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"get_post"})
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get_post"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"get_post"})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Genre::class, inversedBy="posts", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_post"})
     * 
     */
    private $genre;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_post"})
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="posts")
     * @Groups({"get_post"})
     * 
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="post")
     * @Groups({"get_post"})
     */
    private $reviews;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_post"})
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="liked")
     */
    private $likedBy;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->nbLikes = 0;
        $this->nbViews = 0;
        $this->status = 0;
        $this->createdAt = new DateTime();
        $this->likedBy = new ArrayCollection();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getNbViews(): ?int
    {
        return $this->nbViews;
    }

    public function setNbViews(int $nbViews): self
    {
        $this->nbViews = $nbViews;

        return $this;
    }

    public function getNbLikes(): ?int
    {
        return $this->nbLikes;
    }

    public function setNbLikes(int $nbLikes): self
    {
        $this->nbLikes = $nbLikes;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
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

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setPost($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getPost() === $this) {
                $review->setPost(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikedBy(): Collection
    {
        return $this->likedBy;
    }

    public function addLikedPost(User $likedPost): self
    {
        if (!$this->likedBy->contains($likedPost)) {
            $this->likedBy[] = $likedPost;
            $likedPost->addLiked($this);
        }

        return $this;
    }

    public function removeLikedPost(User $likedPost): self
    {
        if ($this->likedBy->removeElement($likedPost)) {
            $likedPost->removeLiked($this);
        }

        return $this;
    }
}
