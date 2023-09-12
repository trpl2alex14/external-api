<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Common\Gateway as BaseGateway;


class Gateway extends BaseGateway
{
    protected string $bodyEncodeMethod = 'http_build_query';


    public function getName(): string
    {
        return 'bitrix24';
    }

}