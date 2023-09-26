<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Contact;


class ContactWithRequisiteResponse extends BatchWithRequisiteResponse
{
    protected string $entityClass = Contact::class;
}