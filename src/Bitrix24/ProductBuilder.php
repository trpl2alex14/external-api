<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Bitrix24\Entities\Product;
use ExternalApi\Bitrix24\Responses\ProductListResponse;
use ExternalApi\Bitrix24\Responses\ProductResponse;
use ExternalApi\Bitrix24\Traits\Filterable;
use ExternalApi\Common\Builder;
use ExternalApi\Contracts\FilterBuilderInterface;


class ProductBuilder extends Builder implements FilterBuilderInterface
{
    use Filterable;

    protected array $methods = [
        'list' => 'crm.product.list',
        'get' => 'crm.product.get',
        'create' => 'crm.product.add',
        'update' => 'crm.product.update',
        'delete' => 'crm.product.delete',
    ];

    protected array $requiredParametersForMethod = [
        'crm.product.get' => 'id',
        'crm.product.update' => 'id',
        'crm.product.delete' => 'id'
    ];

    protected string $entityClass = Product::class;

    protected string $response = ProductResponse::class;


    public function getData(): array
    {
        if ($this->method === 'crm.product.list') {
            $this->response = ProductListResponse::class;
        }

        return match ($this->getMethod()) {
            'crm.product.add' => $this->makeDataForAdd(),
            'crm.product.get' => $this->makeDataForGet(),
            'crm.product.update' => $this->makeDataForUpdate(),
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