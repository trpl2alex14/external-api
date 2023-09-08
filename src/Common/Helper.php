<?php

namespace ExternalApi\Common;


use ExternalApi\Contracts\GatewayInterface;

class Helper
{
    public static function getRequestClassName(string $shortName, $gateway): string
    {
        $gateway = is_object($gateway) ? class_basename($gateway) : $gateway;

        if (is_subclass_of($shortName, Builder::class, true)) {
            return $shortName;
        }

        $namespace = substr($gateway, 0, strrpos($gateway, '\\'));
        return $namespace.'\\'.ucfirst($shortName).'RequestBuilder';
    }


    public static function getGatewayClassName($shortName)
    {
        // If the class starts with ExternalApi\
        if (str_starts_with($shortName, 'ExternalApi\\')) {
            return $shortName;
        }

        // Check if the class exists and implements the Gateway Interface
        if (is_subclass_of($shortName, GatewayInterface::class, true)) {
            return $shortName;
        }

        // replace underscores with namespace marker, PSR-0 style
        $shortName = str_replace('_', '\\', $shortName);
        if (!str_contains($shortName, '\\')) {
            $shortName .= '\\';
        }

        return 'ExternalApi\\'.ucfirst($shortName).'Gateway';
    }
}