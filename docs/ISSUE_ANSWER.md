# Answer to GitHub Issue: Why move away from separate Doctrine Post entity?

## Quick Answer

The repository moved away from separate infrastructure entities to **direct domain mapping** with YML configuration for these key reasons:

### 1. **Eliminated Duplication & Complexity**
- ❌ **Before**: Two entity classes + conversion layer (3 files per entity)  
- ✅ **After**: One domain entity + YML mapping (2 files per entity)

### 2. **Improved Performance**
- ❌ **Before**: Manual conversion between domain ↔ infrastructure entities
- ✅ **After**: Direct hydration/persistence of domain entities

### 3. **Reduced Maintenance**
- ❌ **Before**: Changes required in domain entity, infrastructure entity, AND parser
- ✅ **After**: Changes only in domain entity + mapping file

## Addressing Your Specific Concerns

### 🔧 **Concern 1: Value Objects with Nullable Properties**

**Problem**: "Doctrine can't handle nullable properties when there is a value object, because when hydrating the entity, it then hydrates the value object with null value instead of making the property in the entity null."

**Solution**: Use custom Doctrine types that handle null values properly:

```php
class EmailType extends Type
{
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Email
    {
        return $value === null ? null : new Email($value); // ✅ Returns null instead of Email(null)
    }
}
```

**See**: [`src/Infrastructure/Doctrine/Type/UuidType.php`](src/Infrastructure/Doctrine/Type/UuidType.php) for a complete example.

### 🔧 **Concern 2: Doctrine Collections Breaking DDD**

**Problem**: "When working with relations, you need to use Doctrine Collection in your entities, which I think breaks DDD and hexagonal, because you're using external code in your domain layer."

**Solution**: Use domain interfaces and adapters:

```php
// Domain uses interface, not Doctrine Collection
class Post 
{
    private DomainCollection $comments; // ✅ Domain interface
    
    public function addComment(Comment $comment): void
    {
        $this->comments->add($comment); // ✅ Domain logic
    }
}
```

**See**: [`src/Domain/Shared/Collection/DomainCollection.php`](src/Domain/Shared/Collection/DomainCollection.php) for the interface and implementation.

## Current Architecture Benefits

✅ **Pure Domain Entities**: No Doctrine annotations in domain layer  
✅ **YML Mapping**: Infrastructure concerns separated in mapping files  
✅ **Custom Types**: Proper value object handling  
✅ **Multiple Implementations**: InFile, InMemory, Doctrine repositories  
✅ **Testability**: Easy to test with different implementations  

## When to Use Each Approach

| Scenario | Recommended Approach |
|----------|---------------------|
| Simple entities (like current Post) | ✅ **Direct Domain Mapping** |
| Complex transformations needed | ⚠️ Consider Infrastructure Entities |
| Multiple persistence strategies | ⚠️ Consider Infrastructure Entities |
| Heavy use of value objects | ✅ **Direct Mapping + Custom Types** |
| Complex entity relationships | ✅ **Aggregate Pattern** |

## Complete Documentation

For detailed explanations, examples, and patterns:
- 📖 **[Architecture Decisions](docs/ARCHITECTURE_DECISIONS.md)** - Full analysis of the architectural choice
- 🛠️ **[Practical Examples](docs/EXAMPLES.md)** - Working code examples for value objects and collections

---

**TL;DR**: The move to direct domain mapping reduces complexity while maintaining clean architecture. Value objects and collections can be handled elegantly with custom types and domain interfaces.