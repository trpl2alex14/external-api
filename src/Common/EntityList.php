<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\EntityListInterface;


class EntityList implements EntityListInterface
{
    protected array $items;

    protected string $entityClass = Entity::class;

    public function __construct(array $items, ?string $entityClass = null)
    {
        $this->entityClass = $entityClass ?: $this->entityClass;
        $this->items = array_map(fn($item) => new $this->entityClass($item), $items);
    }


    public function getItems(): iterable
    {
        return $this->items;
    }


    public function getRaw(): array
    {
        return $this->items;
    }
}