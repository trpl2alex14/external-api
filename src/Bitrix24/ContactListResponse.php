<?php

namespace ExternalApi\Bitrix24;


class ContactListResponse extends Response
{
    protected string $entityClass = ContactList::class;


    public function getResult(): ?array
    {
        $response = parent::getResult()['result'] ?? [];

        $response = array_column(array_merge(...array_values($response)), null, 'ID');

        return array_values(array_filter($response, fn($item)=>isset($item['ID'])));
    }
}