<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $password;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $username;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="user")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="user")
     */
    private $reviews;


    /**
     * @ORM\ManyToMany(targetEntity=Post::class)
     * @ORM\JoinTable(name="favoris")
     */
    private $FavoritesPosts;

    /**
     * @ORM\ManyToMany(targetEntity=Post::class)
     * @ORM\JoinTable(name="toReadLater")
     */
    private $toReadPosts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->FavoritesPosts = new ArrayCollection();
        $this->toReadPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

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
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Post>
     */
    public function getFavoritesPosts(): Collection
    {
        return $this->FavoritesPosts;
    }

    public function addFavoritesPost(Post $favoritesPost): self
    {
        if (!$this->FavoritesPosts->contains($favoritesPost)) {
            $this->FavoritesPosts[] = $favoritesPost;
        }

        return $this;
    }

    public function removeFavoritesPost(Post $favoritesPost): self
    {
        $this->FavoritesPosts->removeElement($favoritesPost);

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getToReadPosts(): Collection
    {
        return $this->toReadPosts;
    }

    public function addToReadPost(Post $toReadPost): self
    {
        if (!$this->toReadPosts->contains($toReadPost)) {
            $this->toReadPosts[] = $toReadPost;
        }

        return $this;
    }

    public function removeToReadPost(Post $toReadPost): self
    {
        $this->toReadPosts->removeElement($toReadPost);

        return $this;
    }
}
