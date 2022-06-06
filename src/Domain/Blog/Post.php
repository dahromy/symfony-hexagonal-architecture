<?php

namespace App\Domain\Blog;

use DateTimeInterface;

class Post
{
    private string $uuid;
    private string $title;
    private string $content;
    private ?DateTimeInterface $publishedAt;

    public function __construct(string $title = '', string $content = '', ?DateTimeInterface $publishedAt = null, ?string $uuid = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->publishedAt = $publishedAt;
        $this->uuid = $uuid ?? uniqid();
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return Post
     */
    public function setUuid(string $uuid): Post
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Post
     */
    public function setTitle(string $title): Post
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Post
     */
    public function setContent(string $content): Post
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTimeInterface|null $publishedAt
     * @return Post
     */
    public function setPublishedAt(?DateTimeInterface $publishedAt): Post
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }
}
