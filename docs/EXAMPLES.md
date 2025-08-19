# Examples: Handling Complex Scenarios with Direct Domain Mapping

This document provides practical examples for common scenarios when using direct domain mapping with Doctrine.

## Example 1: Value Objects with Custom Doctrine Types

### Domain Value Object
```php
<?php

namespace App\Domain\Shared\ValueObject;

class Email
{
    private string $value;

    public function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
        
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
```

### Custom Doctrine Type
```php
<?php

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\Shared\ValueObject\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EmailType extends Type
{
    public const NAME = 'email_vo';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Email
    {
        return $value === null ? null : new Email($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value === null ? null : $value->getValue();
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
```

### Configuration
```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            email_vo: App\Infrastructure\Doctrine\Type\EmailType

# Entity mapping
App\Domain\User\User:
    type: entity
    table: users
    id:
        id:
            type: uuid_vo
    fields:
        email:
            type: email_vo
            nullable: true
```

## Example 2: Collections with Domain Interfaces

### Domain Entity with Collections
```php
<?php

namespace App\Domain\Blog\Post;

use App\Domain\Blog\Comment\Comment;
use App\Domain\Shared\Collection\DomainCollection;
use App\Domain\Shared\Collection\ArrayDomainCollection;
use Symfony\Component\Uid\Uuid;

class Post
{
    private Uuid $id;
    private string $title;
    private string $content;
    private DomainCollection $comments;

    public function __construct(Uuid $id, string $title, string $content)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->comments = new ArrayDomainCollection();
    }

    public function addComment(Comment $comment): void
    {
        // Domain logic
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }
    }

    public function removeComment(Comment $comment): void
    {
        $this->comments->remove($comment);
    }

    public function getComments(): DomainCollection
    {
        return $this->comments;
    }

    // ... other methods
}
```

### Doctrine Collection Adapter
```php
<?php

namespace App\Infrastructure\Doctrine\Collection;

use App\Domain\Shared\Collection\DomainCollection;
use Doctrine\Common\Collections\Collection;

class DoctrineDomainCollectionAdapter implements DomainCollection
{
    private Collection $doctrineCollection;

    public function __construct(Collection $doctrineCollection)
    {
        $this->doctrineCollection = $doctrineCollection;
    }

    public function add($element): void
    {
        $this->doctrineCollection->add($element);
    }

    public function remove($element): bool
    {
        return $this->doctrineCollection->removeElement($element);
    }

    public function contains($element): bool
    {
        return $this->doctrineCollection->contains($element);
    }

    public function isEmpty(): bool
    {
        return $this->doctrineCollection->isEmpty();
    }

    public function count(): int
    {
        return $this->doctrineCollection->count();
    }

    public function toArray(): array
    {
        return $this->doctrineCollection->toArray();
    }

    public function getDoctrineCollection(): Collection
    {
        return $this->doctrineCollection;
    }
}
```

### Entity Lifecycle Management
```php
<?php

namespace App\Infrastructure\Doctrine\EventListener;

use App\Domain\Blog\Post\Post;
use App\Infrastructure\Doctrine\Collection\DoctrineDomainCollectionAdapter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;

class PostEntityListener
{
    public function postLoad(Post $post, PostLoadEventArgs $args): void
    {
        // Convert Doctrine collection to domain collection after loading
        $reflection = new \ReflectionClass($post);
        $commentsProperty = $reflection->getProperty('comments');
        $commentsProperty->setAccessible(true);
        
        $doctrineCollection = $commentsProperty->getValue($post);
        if ($doctrineCollection instanceof ArrayCollection) {
            $domainCollection = new DoctrineDomainCollectionAdapter($doctrineCollection);
            $commentsProperty->setValue($post, $domainCollection);
        }
    }

    public function prePersist(Post $post, PrePersistEventArgs $args): void
    {
        // Ensure Doctrine collection is properly set before persistence
        $this->ensureDoctrineCollection($post);
    }

    private function ensureDoctrineCollection(Post $post): void
    {
        $reflection = new \ReflectionClass($post);
        $commentsProperty = $reflection->getProperty('comments');
        $commentsProperty->setAccessible(true);
        
        $domainCollection = $commentsProperty->getValue($post);
        if ($domainCollection instanceof DoctrineDomainCollectionAdapter) {
            return; // Already converted
        }

        // Convert domain collection to Doctrine collection
        $doctrineCollection = new ArrayCollection($domainCollection->toArray());
        $adapter = new DoctrineDomainCollectionAdapter($doctrineCollection);
        $commentsProperty->setValue($post, $adapter);
    }
}
```

## Example 3: Aggregate Pattern for Complex Relationships

### Blog Aggregate
```php
<?php

namespace App\Domain\Blog;

use App\Domain\Blog\Post\Post;
use App\Domain\Blog\Comment\Comment;
use App\Domain\Blog\Tag\Tag;

class BlogAggregate
{
    private Post $post;
    private array $comments = [];
    private array $tags = [];

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function addComment(Comment $comment): void
    {
        // Complex domain logic here
        if ($this->post->isPublished() && !$this->isCommentDuplicate($comment)) {
            $this->comments[] = $comment;
        }
    }

    public function addTag(Tag $tag): void
    {
        if (count($this->tags) < 5 && !$this->hasTag($tag)) {
            $this->tags[] = $tag;
        }
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    private function isCommentDuplicate(Comment $comment): bool
    {
        foreach ($this->comments as $existingComment) {
            if ($existingComment->getContent() === $comment->getContent() &&
                $existingComment->getAuthor() === $comment->getAuthor()) {
                return true;
            }
        }
        return false;
    }

    private function hasTag(Tag $tag): bool
    {
        foreach ($this->tags as $existingTag) {
            if ($existingTag->getName() === $tag->getName()) {
                return true;
            }
        }
        return false;
    }
}
```

### Aggregate Repository
```php
<?php

namespace App\Infrastructure\Blog\Repository;

use App\Domain\Blog\BlogAggregate;
use App\Domain\Blog\Post\Post;
use App\Infrastructure\Blog\Comment\Repository\DoctrineCommentRepository;
use App\Infrastructure\Blog\Tag\Repository\DoctrineTagRepository;
use App\Infrastructure\Post\Repository\DoctrinePostRepository;
use Symfony\Component\Uid\Uuid;

class DoctrineBlogAggregateRepository
{
    private DoctrinePostRepository $postRepository;
    private DoctrineCommentRepository $commentRepository;
    private DoctrineTagRepository $tagRepository;

    public function __construct(
        DoctrinePostRepository $postRepository,
        DoctrineCommentRepository $commentRepository,
        DoctrineTagRepository $tagRepository
    ) {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->tagRepository = $tagRepository;
    }

    public function save(BlogAggregate $aggregate): void
    {
        // Save the main post
        $this->postRepository->save($aggregate->getPost());

        // Save related entities
        foreach ($aggregate->getComments() as $comment) {
            $this->commentRepository->save($comment);
        }

        foreach ($aggregate->getTags() as $tag) {
            $this->tagRepository->save($tag);
        }
    }

    public function findById(Uuid $postId): ?BlogAggregate
    {
        $post = $this->postRepository->findOneById($postId);
        if (!$post) {
            return null;
        }

        $aggregate = new BlogAggregate($post);

        // Load related data
        $comments = $this->commentRepository->findByPostId($postId);
        foreach ($comments as $comment) {
            $aggregate->addComment($comment);
        }

        $tags = $this->tagRepository->findByPostId($postId);
        foreach ($tags as $tag) {
            $aggregate->addTag($tag);
        }

        return $aggregate;
    }
}
```

## Configuration Summary

### Complete Doctrine Configuration
```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            uuid_vo: App\Infrastructure\Doctrine\Type\UuidType
            email_vo: App\Infrastructure\Doctrine\Type\EmailType
    
    orm:
        mappings:
            Post:
                type: yml
                dir: '%kernel.project_dir%/src/Infrastructure/Post/Doctrine/Orm/Mapping'
                prefix: 'App\Domain\Post'
        
        entity_listeners:
            App\Domain\Blog\Post\Post:
                - App\Infrastructure\Doctrine\EventListener\PostEntityListener
```

### Services Configuration
```yaml
# config/services.yaml
services:
    App\Infrastructure\Doctrine\EventListener\PostEntityListener:
        tags:
            - { name: doctrine.orm.entity_listener }
```

These examples show how to maintain clean domain models while working effectively with Doctrine ORM in a hexagonal architecture.