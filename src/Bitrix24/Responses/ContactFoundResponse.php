<?php

namespace ExternalApi\Bitrix24\Responses;


class ContactFoundResponse extends ContactListResponse
{
    public function getBody(): ?array
    {
        $response = parent::getBody() ?? [];
        $lickIds = $this->getLikeContactIds();

        return array_filter($response, fn($contact)=> in_array($contact['ID'], $lickIds));
    }


    public function getLikeContactIds(): array
    {
        $response = $this->body['result']['result'] ?? [];

        $ids = [];
        foreach ($response as $item){
            if(isset($item['CONTACT'])){
                array_push($ids, ...array_values($item['CONTACT']));
            }
        }

        return array_unique($ids);
    }
}