<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Bitrix24\Entities\Requisite;
use ExternalApi\Bitrix24\Responses\RequisiteListResponse;
use ExternalApi\Bitrix24\Responses\RequisiteResponse;
use ExternalApi\Bitrix24\Traits\Filterable;
use ExternalApi\Common\Builder;
use ExternalApi\Contracts\FilterBuilderInterface;


class RequisiteBuilder extends Builder implements FilterBuilderInterface
{
    use Filterable;

    protected array $methods = [
        'list' => 'crm.requisite.list',
        'get' => 'crm.requisite.get',
        'create' => 'crm.requisite.add',
        'update' => 'crm.requisite.update',
        'delete' => 'crm.requisite.delete',
    ];

    protected array $requiredParametersForMethod = [
        'crm.requisite.get' => 'id',
        'crm.requisite.update' => 'id',
        'crm.requisite.delete' => 'id'
    ];

    protected string $entityClass = Requisite::class;

    protected string $response = RequisiteResponse::class;


    public function getData(): array
    {
        if ($this->method === 'crm.requisite.list') {
            $this->response = RequisiteListResponse::class;
        }

        return match ($this->getMethod()) {
            'crm.requisite.add' => $this->makeDataForAdd(),
            'crm.requisite.get' => $this->makeDataForGet(),
            'crm.requisite.update' => $this->makeDataForUpdate(),
            default => parent::getData(),
        };
    }


    private function makeDataForUpdate(): array
    {
        return [
            'id' => $this->getId(),
            'fields' => $this->getFields(),
        ];
    }


    private function makeDataForGet(): array
    {
        return [
            'id' => $this->getId()
        ];
    }


    private function makeDataForAdd(): array
    {
        return [
            'fields' => $this->getFields(),
        ];
    }
}