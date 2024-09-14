<?php

namespace ExternalApi\Bitrix24\Entities;

use ExternalApi\Common\EntityList;


class LeadList extends EntityList
{
    protected string $entityClass = Lead::class;

}