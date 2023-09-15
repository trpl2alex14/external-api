<?php

namespace ExternalApi\Bitrix24\Entities;

use ExternalApi\Common\EntityList;


class ContactList extends EntityList
{
    protected string $entityClass = Contact::class;

}