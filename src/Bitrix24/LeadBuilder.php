<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Bitrix24\Entities\Lead;
use ExternalApi\Bitrix24\Responses\LeadListResponse;
use ExternalApi\Bitrix24\Responses\LeadResponse;
use ExternalApi\Bitrix24\Traits\Filterable;
use ExternalApi\Bitrix24\Traits\Notified;
use ExternalApi\Common\Builder;
use ExternalApi\Contracts\FilterBuilderInterface;


class LeadBuilder extends Builder implements FilterBuilderInterface
{
    use Filterable, Notified;

    protected array $methods = [
        'list' => 'crm.lead.list',
        'get' => 'crm.lead.get',
        'create' => 'crm.lead.add',
        'update' => 'crm.lead.update',
        'delete' => 'crm.lead.delete'
    ];

    protected string $entityClass = Lead::class;

    protected array $requiredParametersForMethod = [
        'crm.lead.get' => 'id',
        'crm.lead.update' => 'id',
        'crm.lead.delete' => 'id'
    ];

    protected string $response = LeadResponse::class;


    public function getData(): array
    {
        if ($this->method === 'crm.lead.list') {
            $this->response = LeadListResponse::class;
        }

        return match ($this->getMethod()) {
            'crm.lead.add' => $this->makeDataForAdd(),
            'crm.lead.get' => $this->makeDataForGet(),
            'crm.lead.update' => $this->makeDataForUpdate(),
            default => parent::getData(),
        };
    }


    private function makeDataForUpdate(): array
    {
        $data = [
            'id' => $this->getId(),
            'fields' => $this->getFields(),
            'params' => []
        ];
        if (!is_null($notify = $this->getNotify())) {
            $data['params']['REGISTER_SONET_EVENT'] = $notify;
        }

        return $data;
    }


    private function makeDataForGet(): array
    {
        return [
            'id' => $this->getId()
        ];
    }


    private function makeDataForAdd(): array
    {
        $data = [
            'fields' => $this->getFields(),
            'params' => []
        ];
        if (!is_null($notify = $this->getNotify())) {
            $data['params']['REGISTER_SONET_EVENT'] = $notify;
        }

        return $data;
    }
}