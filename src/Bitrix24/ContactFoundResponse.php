<?php

namespace ExternalApi\Bitrix24;


class ContactFoundResponse extends Response
{

    public function getResult(): ?array
    {
        $response = parent::getResult()['result'] ?? [];

        $response = array_column(array_merge(...array_values($response)), null, 'ID');

        $contacts = [];

        foreach ($response as $item) {
            if (isset($item['ID'])) {
                $contacts[] = $item;
            }
        }

        return $contacts;
    }
}