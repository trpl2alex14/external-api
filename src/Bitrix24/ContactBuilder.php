<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Common\Builder;
use ExternalApi\Exceptions\BuilderException;


class ContactBuilder extends Builder
{
    protected array $methods = [
        'findBy' => 'crm.duplicate.findbycomm',
        'list' => 'crm.contact.list',
        //'create' => 'crm.contact.add',
        //'update' => 'crm.contact.update',
    ];

    protected string $entityFieldsClass = ContactFields::class;


    public function select(...$fields): self
    {
        $fields = !empty($fields)
            ? array_map(fn($field)=>$this->getEntityFields()->getCode($field), $fields)
            : null;

        return $this
            ->method('list')
            ->setParameter('select', $fields);
    }


    public function where(callable $callable): self
    {
        $filter = $callable(new Filter($this->getEntityFields()));

        return $this->setParameter('filter', $filter);
    }


    public function getData(): array
    {
        return match ($this->getMethod()){
            'crm.duplicate.findbycomm' => $this->findByData(),
            default => parent::getData(),
        };
    }


    private function findByData(): array
    {
        $phone = $this->getParameter('phone');
        $email = $this->getParameter('email');

        if(empty($phone) && empty($email)){
            BuilderException::requiredParameters('phone or email');
        }

        $values = [];
        if(!empty($phone)){
            $phones = is_array($phone) ? $phone: [$phone];

            foreach ($phones as $phone){
                $phone = $this->transformPhoneValue($phone);
                $values[] = '7' . $phone;
                $values[] = '8' . $phone;
            }
        }else{
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