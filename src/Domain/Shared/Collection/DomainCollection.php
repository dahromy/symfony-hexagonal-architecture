<?php

namespace App\Domain\Shared\Collection;

/**
 * Domain-friendly interface for collections
 * Abstracts away Doctrine's Collection interface
 */
interface DomainCollection
{
    public function add($element): void;
    public function remove($element): bool;
    public function contains($element): bool;
    public function isEmpty(): bool;
    public function count(): int;
    public function toArray(): array;
}

/**
 * Generic implementation for domain collections
 */
class ArrayDomainCollection implements DomainCollection
{
    private array $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    public function add($element): void
    {
        $this->elements[] = $element;
    }

    public function remove($element): bool
    {
        $key = array_search($element, $this->elements, true);
        if ($key !== false) {
            unset($this->elements[$key]);
            $this->elements = array_values($this->elements); // Reindex
            return true;
        }
        return false;
    }

    public function contains($element): bool
    {
        return in_array($element, $this->elements, true);
    }

    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function toArray(): array
    {
        return $this->elements;
    }
}