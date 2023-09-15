<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Common\EntityList;


class ContactList extends EntityList
{
    protected string $entityClass = Contact::class;

}