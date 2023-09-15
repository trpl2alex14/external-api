<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Contact;


class ContactAddResponse extends Response
{
    protected string $entityClass = Contact::class;


    public function getResult(): ?array
    {
        $response = parent::getResult();

        return  $response ? ['id' => $response] : null;
    }
}