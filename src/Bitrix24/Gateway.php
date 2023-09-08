<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Common\Gateway as BaseGateway;


class Gateway extends BaseGateway
{
    public function getName(): string
    {
        return 'bitrix24';
    }



}