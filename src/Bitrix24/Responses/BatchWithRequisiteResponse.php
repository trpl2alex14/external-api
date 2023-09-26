<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Requisite;
use ExternalApi\Common\Entity;
use ExternalApi\Common\Helper;


class BatchWithRequisiteResponse extends BatchResponse
{
    public function getBody(): ?array
    {
        $entityName = Helper::getEntityName($this->entityClass);
        $response = parent::getBody()[$entityName . '.add'];

        return $response ? ['id' => $response] : null;
    }


    public function getRequisite(): Entity
    {
        $requisiteId = parent::getBody()['requisite.add'];
        return $this->gateway->createEntity(Requisite::class, ['id' => $requisiteId]);
    }
}