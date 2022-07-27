<?php

namespace App\Application\UseCase\Command\Post\Create;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use InvalidArgumentException;
use function sprintf;

class CreatePostInputDataTransformer implements DataTransformerInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param object $object
     * @param string $to
     * @param array<string, mixed> $context
     *
     * @return CreatePostCommand
     */
    public function transform($object, string $to, array $context = []): CreatePostCommand
    {
        if (!$object instanceof CreatePostInput) {
            throw new InvalidArgumentException(sprintf('Object is not an instance of %s', CreatePostInput::class));
        }

        $this->validator->validate($object, $context);

        return new CreatePostCommand(
            $object->title,
            $object->content,
            $object->publishedAt
        );
    }

    /**
     * @param mixed $data
     * @param string $to
     * @param array<string, array<string>> $context
     *
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return CreatePostInput::class === ($context['input']['class'] ?? null);
    }
}
