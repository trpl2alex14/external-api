<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Bitrix24\Entities\Product;
use ExternalApi\Bitrix24\Responses\ProductIdResponse;
use ExternalApi\Bitrix24\Responses\ProductListResponse;
use ExternalApi\Bitrix24\Responses\ProductResponse;
use ExternalApi\Bitrix24\Traits\Filterable;
use ExternalApi\Common\Builder;


class ProductBuilder extends Builder
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


    public function getData(): array
    {
        $this->response = ProductListResponse::class;

        return match ($this->getMethod()) {
            'crm.product.add' => $this->makeDataForAdd(),
            'crm.product.get' => $this->makeDataForGet(),
            'crm.product.update' => $this->makeDataForUpdate(),
            default => parent::getData(),
        };
    }


    private function makeDataForUpdate(): array
    {
        $data = [
            'id' => $this->getId(),
            'fields' => $this->getFields(),
        ];

        $this->response = ProductIdResponse::class;

        return $data;
    }


    private function makeDataForGet(): array
    {
        $data = [
            'id' => $this->getId()
        ];

        $this->response = ProductResponse::class;

        return $data;
    }


    private function makeDataForAdd(): array
    {
        $data = [
            'fields' => $this->getFields(),
        ];

        $this->response = ProductIdResponse::class;

        return $data;
    }
}