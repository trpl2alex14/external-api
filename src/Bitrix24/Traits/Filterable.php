<?php

namespace ExternalApi\Bitrix24\Traits;

use ExternalApi\Bitrix24\Filter;


trait Filterable
{
    public function select(...$fields): self
    {
        $fields = !empty($fields) && !is_null($fields[0])
            ? array_map(fn($field) => $this->getEntity()->getCode($field), $fields)
            : null;

        return $this
            ->method('list')
            ->setParameter('select', $fields);
    }


    public function where(callable $callable): self
    {
        $filter = $callable(new Filter($this->getEntity()));

        return $this->setParameter('filter', $filter->getData());
    }
}