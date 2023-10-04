<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Common\Builder;

/// https://****.bitrix24.ru/rest/userID/*****/crm.automation.trigger/?code=****&target=DEAL_**
class TriggerBuilder extends Builder
{
    protected string $method = 'crm.automation.trigger';

    public function getData(): array
    {
        $this->setQuery([
            'code' => $this->getParameter('code'),
            'target' => strtoupper($this->getParameter('target')) . '_' . $this->getId()
        ]);

        return [];
    }
}