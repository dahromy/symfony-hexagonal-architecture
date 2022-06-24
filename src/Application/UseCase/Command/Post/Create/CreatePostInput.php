<?php

namespace App\Application\UseCase\Command\Post\Create;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePostInput
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(min=3)
     */
    public string $title;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     * @Assert\Length(min=10)
     */
    public string $content;

    /**
     * @Assert\Type("datetime")
     */
    public ?DateTime $publishedAt;
}
