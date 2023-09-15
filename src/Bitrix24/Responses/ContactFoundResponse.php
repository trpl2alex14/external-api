<?php

namespace ExternalApi\Bitrix24\Responses;


class ContactFoundResponse extends ContactListResponse
{
    public function getLikeContactIds(): array
    {
        $response = $this->getBody()['result']['result'] ?? [];

        $ids = [];
        foreach ($response as $item){
            if(isset($item['CONTACT'])){
                array_push($ids, ...array_values($item['CONTACT']));
            }
        }

        return array_unique($ids);
    }
}