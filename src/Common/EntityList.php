<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\EntityListInterface;


class EntityList extends Entity implements EntityListInterface
{
    protected array $items;

    protected string $entityClass = Entity::class;


    public function __construct(array $fields = [], ?array $settingFields = null)
    {
        $items = array_map(fn($item) => new $this->entityClass($item, $settingFields), $fields);

        parent::__construct(['items' => $items], $settingFields);
    }


    public function getItems(): iterable
    {
        return $this->getField('items');
    }
}