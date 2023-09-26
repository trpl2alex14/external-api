<?php

namespace ExternalApi\Bitrix24\Traits;

use ExternalApi\Bitrix24\BatchBuilder;
use ExternalApi\Bitrix24\Entities\Requisite;
use ExternalApi\Common\Builder;
use ExternalApi\Common\Entity;
use ExternalApi\Exceptions\BuilderException;


trait WithRequisite
{
    public function setRequisite(?Requisite $requisite): self
    {
        return $this->setParameter('requisite', $requisite);
    }


    public function addRequisite(bool $add = true): self
    {
        return $this->setParameter('addRequisite', $add);
    }


    public function hasRequisite(): bool
    {
        return !!$this->getParameter('requisite') || $this->getParameter('addRequisite');
    }

    /**
     * @throws BuilderException
     */
    protected function makeRequisiteBatchBuilder(string $forEntityWithName, ?array $needFields = null): ?BatchBuilder
    {
        $requisite = $this->getParameter('requisite');

        if (empty($requisite)) {
            $entity = $this->gateway->createEntity($forEntityWithName, $this->getFields());

            $requisite = $this->makeRequisiteEntity($entity, $needFields);
        }

        $requisiteBuilder = $this->gateway
            ->createRequestBuilder('requisite')
            ->method('create')
            ->setFields($requisite->getFields());

        return $this->makeBatch($forEntityWithName, $requisiteBuilder);
    }


    protected function makeRequisiteEntity(Entity $fromEntity, ?array $needFields = null)
    {
        $requisite = $this->gateway->createEntity('requisite');

        $fields = [];
        $needFields = $needFields ?: array_flip(array_keys($requisite->getCodeFields()));
        foreach ($needFields as $from => $to) {
            $fields[is_numeric($to) ? $from : $to] = $fromEntity->getField($from);
        }
        $fields = array_filter($fields);

        $requisite->setFields($fields);
        return $requisite;
    }

    /**
     * @throws BuilderException
     */
    private function makeBatch(string $entityName, Builder $requisiteBuilder): BatchBuilder
    {
        $batch = new BatchBuilder();

        $batch->setCommand(
            (clone $this)
                ->setRequisite(null)
                ->addRequisite(false),
            $entityName . '.add'
        );

        $batch->setCommand($requisiteBuilder, 'requisite.add');
        $batch->takeInputFrom($entityName . '.add', null, 'fields[ENTITY_ID]', 'ID');

        return $batch;
    }
}