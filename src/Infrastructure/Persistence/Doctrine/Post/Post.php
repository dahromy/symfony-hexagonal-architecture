<?php

namespace App\Infrastructure\Persistence\Doctrine\Post;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=PostDoctrineRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private ?Uuid $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $content;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $publishedAt;

    /**
     * Post constructor.
     *
     * @param Uuid|null $uuid
     */
    public function __construct(?Uuid $uuid = null)
    {
        $this->id = $uuid;
    }

    public function getId(): ?Uuid
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

    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }
}
