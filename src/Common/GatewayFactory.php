<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\GatewayInterface;
use RuntimeException;


class GatewayFactory
{
    /**
     * @param string $name
     * @return GatewayInterface
     * @throws RuntimeException
     */
    public function create(string $name): GatewayInterface
    {
        $class = Helper::getGatewayClassName($name);

        if (!class_exists($class)) {
            throw new RuntimeException("Class '$class' not found");
        }

        return new $class();
    }
}