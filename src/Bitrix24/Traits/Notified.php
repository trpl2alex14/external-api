<?php

namespace ExternalApi\Bitrix24\Traits;


trait Notified
{
    public function setNotify(bool $notify): self
    {
        return $this->setParameter('notify', $notify ? 'Y' : 'N');
    }


    public function getNotify(): ?string
    {
        return $this->getParameter('notify');
    }
}