<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Bitrix24\Entities\Company;
use ExternalApi\Bitrix24\Responses\CompanyListResponse;
use ExternalApi\Bitrix24\Responses\CompanyResponse;
use ExternalApi\Bitrix24\Responses\CompanyWithRequisiteResponse;
use ExternalApi\Bitrix24\Traits\Filterable;
use ExternalApi\Bitrix24\Traits\Notified;
use ExternalApi\Bitrix24\Traits\WithRequisite;
use ExternalApi\Common\Builder;
use ExternalApi\Contracts\FilterBuilderInterface;
use ExternalApi\Exceptions\BuilderException;


class CompanyBuilder extends Builder implements FilterBuilderInterface
{
    use Filterable, Notified, WithRequisite;

    protected array $methods = [
        'create' => 'crm.company.add',
        'get' => 'crm.company.get',
        'update' => 'crm.company.update',
        'list' => 'crm.company.list',
        'delete' => 'crm.company.delete',
    ];

    protected string $entityClass = Company::class;

    protected array $requiredParametersForMethod = [
        'crm.company.get' => 'id',
        'crm.company.update' => 'id',
        'crm.company.delete' => 'id',
    ];

    protected string $response = CompanyResponse::class;


    public function getData(): array
    {
        if ($this->method === 'crm.company.list') {
            $this->response = CompanyListResponse::class;
        }

        return match ($this->getMethod()) {
            'crm.company.add' => $this->makeDataForAdd(),
            'crm.company.get' => $this->makeDataForGet(),
            'crm.company.update' => $this->makeDataForUpdate(),
            default => parent::getData(),
        };
    }

    /**
     * @throws BuilderException
     */
    private function makeDataForAdd(): array
    {
        if ($this->hasRequisite()) {
            $builder = $this->makeRequisiteBatchBuilder('company');
            $this->method('batch')
                ->setResponse(CompanyWithRequisiteResponse::class);

            return $builder->getData();
        }

        $data = [
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
}