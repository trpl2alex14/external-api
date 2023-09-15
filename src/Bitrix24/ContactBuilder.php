<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Common\Builder;
use ExternalApi\Contracts\EntityInterface;
use ExternalApi\Contracts\FilterInterface;
use ExternalApi\Contracts\RequestBuilderInterface;
use ExternalApi\Contracts\SearchContactInterface;
use ExternalApi\Exceptions\BuilderException;


class ContactBuilder extends Builder
{
    protected array $methods = [
        'findBy' => 'crm.duplicate.findbycomm',
        'list' => 'crm.contact.list',
        //'create' => 'crm.contact.add',
        //'update' => 'crm.contact.update',
    ];

    protected string $entityClass = Contact::class;


    public function select(...$fields): self
    {
        $fields = !empty($fields) && !is_null($fields[0])
            ? array_map(fn($field) => $this->getEntity()->getCode($field), $fields)
            : null;

        return $this
            ->method('list')
            ->setParameter('select', $fields);
    }


    public function where(callable $callable): self
    {
        $filter = $callable(new Filter($this->getEntity()));

        return $this->setParameter('filter', $filter->getData());
    }

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

        return $batch->setResponse(ContactFoundResponse::class);
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


    public function getData(): array
    {
        return match ($this->getMethod()) {
            'crm.duplicate.findbycomm' => $this->findByData(),
            default => parent::getData(),
        };
    }


    private function findByData(): array
    {
        $phone = $this->getParameter('phone');
        $email = $this->getParameter('email');

        if (empty($phone) && empty($email)) {
            BuilderException::requiredParameters('phone or email');
        }

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
        return substr(preg_replace("/[^0-9]/", '', $phone), 1);
    }
}