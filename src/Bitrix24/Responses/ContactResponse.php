<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Contact;


class ContactResponse extends Response
{
    protected string $entityClass = Contact::class;

}