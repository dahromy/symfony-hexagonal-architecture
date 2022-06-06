<?php

namespace App\Application\Blog\CreatePost;

use DateTimeInterface;

class CreatePostCommand
{
    private string $title;
    private string $content;
    private ?DateTimeInterface $publishedAt;

    public function __construct(string $title, string $content, ?DateTimeInterface $publishedAt = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->publishedAt = $publishedAt;
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
     * @return CreatePostCommand
     */
    public function setTitle(string $title): CreatePostCommand
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
     * @return CreatePostCommand
     */
    public function setContent(string $content): CreatePostCommand
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
     * @return CreatePostCommand
     */
    public function setPublishedAt(?DateTimeInterface $publishedAt): CreatePostCommand
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }
}
