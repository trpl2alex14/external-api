<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Bitrix24\Entities\Contact;
use ExternalApi\Bitrix24\Responses\ContactFoundResponse;
use ExternalApi\Bitrix24\Responses\ContactListResponse;
use ExternalApi\Bitrix24\Responses\ContactResponse;
use ExternalApi\Bitrix24\Responses\ContactWithRequisiteResponse;
use ExternalApi\Bitrix24\Traits\Filterable;
use ExternalApi\Bitrix24\Traits\Notified;
use ExternalApi\Bitrix24\Traits\WithRequisite;
use ExternalApi\Common\Builder;
use ExternalApi\Common\Helper;
use ExternalApi\Contracts\EntityInterface;
use ExternalApi\Contracts\FilterBuilderInterface;
use ExternalApi\Contracts\FilterInterface;
use ExternalApi\Contracts\RequestBuilderInterface;
use ExternalApi\Contracts\SearchContactInterface;
use ExternalApi\Exceptions\BuilderException;


class ContactBuilder extends Builder implements FilterBuilderInterface
{
    use Filterable, Notified, WithRequisite;

    protected array $methods = [
        'findBy' => 'crm.duplicate.findbycomm',
        'list' => 'crm.contact.list',
        'get' => 'crm.contact.get',
        'create' => 'crm.contact.add',
        'update' => 'crm.contact.update',
        'delete' => 'crm.contact.delete',
    ];

    protected string $entityClass = Contact::class;

    protected array $requiredParametersForMethod = [
        'crm.contact.get' => 'id',
        'crm.contact.update' => 'id',
        'crm.contact.delete' => 'id',
        'crm.duplicate.findbycomm' => 'phone|email'
    ];

    protected string $response = ContactResponse::class;

    /**
     * @throws BuilderException
     */
    public function byContact(EntityInterface|SearchContactInterface|array $contact): RequestBuilderInterface
    {
        $contact = $this->makeContactArray($contact);

        if (empty($contact)) {
            throw BuilderException::requiredParameters('contact');
        }

        $searchBuilders = $this->makeSearchBuilders($contact);

        if (empty($searchBuilders)) {
            throw BuilderException::requiredParameters('contact phone or email');
        }

        $contactListBuilder = (new ContactBuilder())
            ->select($this->getParameter('select'))
            ->where(function (FilterInterface $filter) use ($contact) {
                $filter = isset($contact['first_name'])
                    ? $filter->contains('first_name', $contact['first_name'])
                    : $filter;

                return isset($contact['last_name'])
                    ? $filter->equal('last_name', $contact['last_name'])
                    : $filter;
            });

        $batch = new BatchBuilder();
        foreach ($searchBuilders as $name => $builder) {
            $batch
                ->setCommand($builder, $name)
                ->setCommand($contactListBuilder, 'list.' . $name)
                ->takeInputFrom($name, null, 'filter[ID]', 'CONTACT');
        }

        return $batch->setResponse(ContactFoundResponse::class)->setGateway($this->gateway);
    }


    private function makeContactArray(SearchContactInterface|array $contact)
    {
        return array_filter([
            'email' => $contact instanceof SearchContactInterface ? $contact->getEmail() : $contact['email'] ?? null,
            'phone' => $contact instanceof SearchContactInterface ? $contact->getPhone() : $contact['phone'] ?? null,
            'first_name' => $contact instanceof SearchContactInterface ? $contact->getFirstName() : $contact['first_name'] ?? null,
            'last_name' => $contact instanceof SearchContactInterface ? $contact->getLastName() : $contact['last_name'] ?? null,
        ]);
    }


    private function makeSearchBuilders(array $contact): array
    {
        $searchBuilders = [];
        foreach (['email', 'phone'] as $type) {
            if (!empty($contact[$type])) {
                $searchBuilders['find.' . $type] = (new ContactBuilder())
                    ->method('findBy')
                    ->setParameter($type, $contact[$type]);
            }
        }

        return $searchBuilders;
    }


    /**
     * @throws BuilderException
     */
    public function getData(): array
    {
        if ($this->method === 'crm.contact.list') {
            $this->response = ContactListResponse::class;
        }

        return match ($this->getMethod()) {
            'crm.duplicate.findbycomm' => $this->makeDataForFindBy(),
            'crm.contact.add' => $this->makeDataForAdd(),
            'crm.contact.get' => $this->makeDataForGet(),
            'crm.contact.update' => $this->makeDataForUpdate(),
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

    /**
     * @throws BuilderException
     */
    private function makeDataForAdd(): array
    {
        if ($this->hasRequisite()) {
            $builder = $this->makeRequisiteBatchBuilder('contact');
            $this->method('batch')
                ->setResponse(ContactWithRequisiteResponse::class);

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


    private function makeDataForFindBy(): array
    {
        $phone = $this->getParameter('phone');
        $email = $this->getParameter('email');

        $values = [];
        if (!empty($phone)) {
            $phones = is_array($phone) ? $phone : [$phone];

            foreach ($phones as $phone) {
                $phone = $this->transformPhoneValue($phone);
                $values[] = '7' . $phone;
                $values[] = '8' . $phone;
            }
        } else {
            $values = is_array($email) ? $email : [$email];
        }

        return [
            "entity_type" => "CONTACT",
            "type" => $phone ? "PHONE" : "EMAIL",
            "values" => $values
        ];
    }


    private function transformPhoneValue(string $phone): string
    {
        return Helper::formatPhone($phone);
    }
}