<?php

namespace ExternalApi\Bitrix24\Entities;


class Lead extends Contact
{
    protected array $fieldCodes = [
        'id' => 'ID',
        'title' => 'TITLE',
        'name' => 'NAME',
        'first_name' => 'NAME',
        'last_name' => 'LAST_NAME',
        'second_name' => 'SECOND_NAME',
        'phone' => 'PHONE',
        'email' => 'EMAIL',
        'comments' => 'COMMENTS',
        'source_id' => 'SOURCE_ID',
        'stage_id' => 'STAGE_ID',
    ];
}