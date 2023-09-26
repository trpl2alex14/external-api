<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Contact;
use ExternalApi\Bitrix24\Entities\Requisite;
use ExternalApi\Common\Entity;


class ContactWithRequisiteResponse extends BatchResponse
{
    protected string $entityClass = Contact::class;

    public function getBody(): ?array
    {
        $response = parent::getBody()['contact.add'];

        return $response ? ['id' => $response] : null;
    }


    public function getRequisite(): Entity
    {
        $requisiteId = parent::getBody()['requisite.add'];
        return $this->gateway->createEntity(Requisite::class, ['id' => $requisiteId]);
    }
}