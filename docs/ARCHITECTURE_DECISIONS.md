# Architecture Decisions: From Infrastructure Entities to Direct Domain Mapping

## Question: Why move away from separate Doctrine Post entity?

This document addresses the architectural decision made in commit [7a339cb](https://github.com/dahromy/symfony-hexagonal-architecture/commit/7a339cb61b1b11d642cccda5376a47ef6358fcb4) to move away from separate infrastructure entities to direct domain entity mapping with Doctrine ORM.

## The Problem with Separate Infrastructure Entities

### Before: Infrastructure Entity Approach

Previously, the repository used a pattern with:
- `App\Domain\Post\Post` - Pure domain entity
- `App\Infrastructure\Post\Doctrine\Post` - Infrastructure entity with Doctrine annotations
- `PostDoctrineParser` - Converter between domain and infrastructure entities

```php
// Old approach - Infrastructure entity
/**
 * @ORM\Entity(repositoryClass=DoctrinePostRepository::class)
 */
class Post // Infrastructure entity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private ?Uuid $id;
    
    // ... other properties with Doctrine annotations
}

// Conversion layer
class PostDoctrineParser
{
    public function toDomain(PostEntity $postEntity): Post
    {
        return new Post(
            $postEntity->getId() ? $postEntity->getId()->toRfc4122() : '',
            $postEntity->getTitle() ?? '',
            $postEntity->getContent() ?? '',
            $postEntity->getPublishedAt(),
        );
    }

    public function toDoctrine(Post $post): PostEntity
    {
        $postEntity = new PostEntity(Uuid::v6()::fromString($post->getId()));
        
        $postEntity
            ->setTitle($post->getTitle())
            ->setContent($post->getContent())
            ->setPublishedAt($post->getPublishedAt());
            
        return $postEntity;
    }
}
```

### Issues with This Approach

1. **Duplication**: Two similar entity classes with nearly identical properties
2. **Maintenance Overhead**: Changes required in multiple places for a single domain concept
3. **Complex Conversion**: Manual mapping between domain and infrastructure entities
4. **Performance Impact**: Additional object creation and property copying
5. **Error Prone**: Easy to miss fields during conversion or make mapping mistakes

## The Solution: Direct Domain Mapping

### After: YML Mapping Approach

The refactored approach maps Doctrine directly to domain entities using YML configuration:

```yaml
# src/Infrastructure/Post/Doctrine/Orm/Mapping/Post.orm.yml
App\Domain\Post\Post:
    type: entity
    repositoryClass: App\Infrastructure\Post\Repository\DoctrinePostRepository
    table: post
    id:
        id:
            type: uuid
            unique: true
    fields:
        title:
            type: string
            length: 50
        content:
            type: text
        publishedAt:
            type: datetime
            nullable: true
```

```php
// Domain entity remains pure
class Post
{
    private Uuid $id;
    private string $title;
    private string $content;
    private ?DateTimeInterface $publishedAt;

    public function __construct(Uuid $id, string $title, string $content, ?DateTimeInterface $publishedAt)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->publishedAt = $publishedAt;
    }
    
    // ... pure domain methods without persistence concerns
}

// Repository works directly with domain entities
class DoctrinePostRepository extends ServiceEntityRepository implements PostRepositoryInterface
{
    public function save(Post $post): void
    {
        $this->add($post, true);
    }

    public function findOneById(Uuid $id): ?Post
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $id->toBinary())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
```

### Benefits of Direct Mapping

1. **Simplicity**: Single entity class represents domain concept
2. **No Duplication**: One source of truth for entity structure
3. **Better Performance**: No conversion overhead
4. **Easier Maintenance**: Changes made in one place
5. **Cleaner Code**: Less boilerplate and conversion logic

## Addressing Common Concerns

### 1. Value Objects and Nullable Properties

**Concern**: "Doctrine can't handle nullable properties when there is a value object, because when hydrating the entity, it then hydrates the value object with null value instead of making the property in the entity null."

**Solutions**:

#### Option A: Custom Doctrine Types
```php
// Create custom Doctrine type for value objects
class EmailType extends Type
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : new Email($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : $value->getValue();
    }
}

// Register in doctrine.yaml
doctrine:
    dbal:
        types:
            email: App\Infrastructure\Doctrine\Type\EmailType
```

#### Option B: Embeddables with Nullable Handling
```php
class User
{
    private UserId $id;
    private ?Email $email; // Nullable value object

    public function __construct(UserId $id, ?string $email = null)
    {
        $this->id = $id;
        $this->email = $email ? new Email($email) : null;
    }
}

# Mapping
App\Domain\User\User:
    type: entity
    id:
        id:
            type: user_id
    fields:
        email:
            type: email
            nullable: true
```

#### Option C: Lifecycle Callbacks
```php
class Post
{
    private Uuid $id;
    private ?Title $title;

    /**
     * @PostLoad
     */
    public function postLoad(): void
    {
        // Handle null value objects after Doctrine hydration
        if ($this->title !== null && !($this->title instanceof Title)) {
            $this->title = new Title($this->title);
        }
    }
}
```

### 2. Doctrine Collections in Domain Entities

**Concern**: "When working with relations, you need to use Doctrine Collection in your entities, which I think breaks DDD and hexagonal, because you're using external code in your domain layer."

**Solutions**:

#### Option A: Array Collections with Conversion
```php
class Post
{
    private Uuid $id;
    private array $comments = []; // Pure PHP array in domain

    public function addComment(Comment $comment): void
    {
        $this->comments[] = $comment;
    }

    public function getComments(): array
    {
        return $this->comments;
    }
}

// In repository or service
class DoctrinePostRepository
{
    public function save(Post $post): void
    {
        // Convert array to Doctrine Collection if needed
        if (is_array($post->getComments())) {
            $collection = new ArrayCollection($post->getComments());
            // Use reflection to set the collection
        }
        
        $this->getEntityManager()->persist($post);
        $this->getEntityManager()->flush();
    }
}
```

#### Option B: Interface Abstraction
```php
interface CommentCollection
{
    public function add(Comment $comment): void;
    public function remove(Comment $comment): void;
    public function toArray(): array;
}

class Post
{
    private Uuid $id;
    private CommentCollection $comments;

    public function __construct(Uuid $id, CommentCollection $comments = null)
    {
        $this->id = $id;
        $this->comments = $comments ?? new ArrayCommentCollection();
    }
}

// Implementation for Doctrine
class DoctrineCommentCollection implements CommentCollection
{
    private Collection $collection;
    
    public function __construct(Collection $collection = null)
    {
        $this->collection = $collection ?? new ArrayCollection();
    }
}
```

#### Option C: Aggregate Boundaries
```php
// Instead of managing collections in entities, use aggregates
class PostAggregate
{
    private Post $post;
    private array $comments = [];

    public function addComment(Comment $comment): void
    {
        // Domain logic here
        $this->comments[] = $comment;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getComments(): array
    {
        return $this->comments;
    }
}

// Repository manages the aggregate
interface PostAggregateRepository
{
    public function save(PostAggregate $aggregate): void;
    public function findById(Uuid $id): ?PostAggregate;
}
```

## Recommended Approach

### For Simple Entities (like the current Post)
Use direct domain mapping with YML configuration:
- Keep domain entities pure and simple
- Use YML mapping to avoid persistence annotations in domain
- Handle value objects with custom Doctrine types when needed

### For Complex Aggregates with Collections
Consider a hybrid approach:
- Use domain entities for simple cases
- Use aggregate pattern for complex entity relationships
- Keep collections as arrays in domain, convert in repositories
- Use interfaces to abstract collection implementations

### When to Use Separate Infrastructure Entities
Consider separate infrastructure entities only when:
- Complex transformations are needed between domain and persistence
- Multiple persistence strategies are required
- Domain model is significantly different from database schema
- Performance optimization requires different object structures

## Conclusion

The move away from separate Doctrine entities to direct domain mapping represents a pragmatic choice that:
- Reduces complexity for simple domain models
- Maintains clean architecture principles
- Provides practical solutions for common ORM challenges
- Allows for evolution as domain complexity grows

The key is choosing the right approach based on your domain complexity and requirements, rather than following rigid architectural patterns that may add unnecessary complexity.