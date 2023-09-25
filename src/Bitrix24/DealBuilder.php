<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Bitrix24\Entities\Contact;
use ExternalApi\Bitrix24\Entities\Deal;
use ExternalApi\Bitrix24\Entities\ProductRow;
use ExternalApi\Bitrix24\Responses\DealBatchResponse;
use ExternalApi\Bitrix24\Responses\DealIdResponse;
use ExternalApi\Bitrix24\Responses\DealListResponse;
use ExternalApi\Bitrix24\Responses\DealResponse;
use ExternalApi\Bitrix24\Traits\Filterable;
use ExternalApi\Bitrix24\Traits\Notified;
use ExternalApi\Common\Builder;
use ExternalApi\Exceptions\BuilderException;


class DealBuilder extends Builder
{
    use Filterable, Notified;

    protected array $methods = [
        'create' => 'crm.deal.add',
        'get' => 'crm.deal.get',
        'update' => 'crm.deal.update',
        'list' => 'crm.deal.list',
        'delete' => 'crm.deal.delete',
    ];

    protected string $entityClass = Deal::class;

    protected array $requiredParametersForMethod = [
        'crm.deal.get' => 'id',
        'crm.deal.update' => 'id',
        'crm.deal.delete' => 'id',
    ];

    /**
     * @param Contact[]|int[] $clients
     *
     * @return $this
     */
    public function setClients(array $clients): self
    {
        return $this->setParameter('clients', $clients);
    }

    /**
     * @param ProductRow[]|array $items
     *
     * @return $this
     */
    public function setItems(array $items): self
    {
        return $this->setParameter('items', $items);
    }


    public function setCompany(array|int $company): self
    {
        $company = is_int($company) ? $company : $company['id'];
        return $this->setParameter('company', $company);
    }

    /**
     * @throws BuilderException
     */
    public function getData(): array
    {
        if ($company = $this->getParameter('company')) {
            $this->parameters['fields'] = $this->parameters['fields'] ?? [];

            $this->parameters['fields']['COMPANY_ID'] = $company;
        }

        $builders = $this->makeDealBuilders();

        if (!empty($builders)) {
            $batchData = $this->makeBatch($builders)->getData();

            $this->method('batch')->setResponse(DealBatchResponse::class);

            return $batchData;
        }

        $this->response = DealListResponse::class;

        return match ($this->getMethod()) {
            'crm.deal.add' => $this->makeDataForAdd(),
            'crm.deal.get' => $this->makeDataForGet(),
            'crm.deal.update' => $this->makeDataForUpdate(),
            default => parent::getData(),
        };
    }


    private function makeDealBuilders(): array
    {
        $batchBuilders = [
            'contact.set' => $this->getContactsBuilder(),
            'productrows.set' => $this->getProductrowsBuilder(),
        ];

        return array_filter($batchBuilders);
    }


    private function getContactsBuilder(): ?Builder
    {
        if (is_null($clients = $this->getParameter('clients'))) {
            return null;
        }

        $clients = array_map(fn($client) => [
            'CONTACT_ID' => $client instanceof Contact ? $client->id : $client,
        ], $clients);

        $clients[0]['IS_PRIMARY'] = 'Y';

        return (new Builder())
            ->method('crm.deal.contact.items.set')
            ->setParameters(['items' => $clients]);
    }


    private function getProductrowsBuilder(): ?Builder
    {
        if (is_null($items = $this->getParameter('items'))) {
            return null;
        }

        $items = array_map(fn(array|ProductRow $item) => $item instanceof ProductRow
            ? $item->getFields()
            : array_filter([
                'QUANTITY' => $item['quantity'] ?? 1,
                'PRODUCT_NAME' => $item['name'] ?? null,
                'PRODUCT_ID' => $item['id'] ?? null,
                'PRICE' => $item['price'] ?? null,
                'DISCOUNT_SUM' => $item['discount'] ?? null,
                'TAX_RATE' => $item['tax'] ?? null,
                'TAX_INCLUDED' => $item['tax_included'] ?? 'Y',
            ]), $items);

        return (new Builder())
            ->method('crm.deal.productrows.set')
            ->setParameters(['rows' => $items]);
    }


    private function makeBatch(array $batchBuilders): BatchBuilder
    {
        $batch = new BatchBuilder();

        $batch->setCommand(
            (clone $this)
                ->setParameter('clients', null)
                ->setParameter('items', null),
            'deal.add'
        );

        foreach ($batchBuilders as $name => $builder) {
            $batch->setCommand($builder, $name);
            $batch->takeInputFrom('deal.add', null, 'id', 'ID');
        }

        return $batch;
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

        $this->response = DealIdResponse::class;

        return $data;
    }


    private function makeDataForGet(): array
    {
        $data = [
            'id' => $this->getId()
        ];

        $this->response = DealResponse::class;

        return $data;
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

        $this->response = DealIdResponse::class;

        return $data;
    }
}