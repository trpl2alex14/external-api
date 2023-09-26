<?php

namespace ExternalApi\Bitrix24\Entities;

use ExternalApi\Common\Entity;


class Requisite extends Entity
{
    protected array $entityType = [
        4 => 'Организация/ИП',
        3 => 'Физ. лицо',
    ];

    protected array $presetType = [
        2 => 'Организация',
        4 => 'ИП',
        6 => 'Физ. лицо',
    ];

    protected array $fieldCodes = [
        'id' => 'ID',
        'name' => 'NAME',
        'entity_id' => 'ENTITY_ID',
        'entity_type_id' => 'ENTITY_TYPE_ID',
        'preset_id' => 'PRESET_ID',
        'NAME' => 'NAME',
        'inn' => 'RQ_INN',
        'kpp' => 'RQ_KPP',
        'ogrn' => 'RQ_OGRN',
        'ogrnip' => 'RQ_OGRNIP',
        'director' => 'RQ_DIRECTOR',
        'full_name' => 'RQ_COMPANY_FULL_NAME',
        'first_name' => 'RQ_FIRST_NAME',
        'last_name' => 'RQ_LAST_NAME',
        'second_name' => 'RQ_SECOND_NAME',
    ];

    protected array $fields = [
        'PRESET_ID' => 6,
        'ENTITY_TYPE_ID' => 3,
        'NAME' => 'Реквизит'
    ];


    public function setInn(?string $value): self
    {
        if(empty($value)){
            $this
                ->setField('preset_id', 6)
                ->setField('entity_type_id', 3);
        }else{
            $this
                ->setField('preset_id', strlen($value) > 10 ? 4 : 2)
                ->setField('entity_type_id', 4);
        }

        return $this->setField('inn', $value);
    }


    public function getEntityTypes(): array
    {
        return $this->entityType;
    }


    public function getPresetTypes(): array
    {
        return $this->presetType;
    }
}