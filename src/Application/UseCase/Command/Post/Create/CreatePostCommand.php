<?php

namespace App\Application\UseCase\Command\Post\Create;

use DateTimeInterface;

class CreatePostCommand
{
    private string $title;
    private string $content;
    private ?DateTimeInterface $publishedAt;

    public function __construct(string $title, string $content, ?DateTimeInterface $publishedAt = NULL)
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
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }
}
