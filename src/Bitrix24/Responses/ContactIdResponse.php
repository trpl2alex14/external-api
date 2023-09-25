<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Contact;


class ContactIdResponse extends IdResponse
{
    protected string $entityClass = Contact::class;
}