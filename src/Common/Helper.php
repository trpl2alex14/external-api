<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\GatewayInterface;
use ReflectionClass;


class Helper
{
    /**
     * @throws \ReflectionException
     */
    public static function getRequestClassName(string $shortName, $gateway): string
    {
        $gateway = is_object($gateway) ? (new ReflectionClass($gateway))->getName() : $gateway;

        if (is_subclass_of($shortName, Builder::class, true)) {
            return $shortName;
        }

        $namespace = substr($gateway, 0, strrpos($gateway, '\\'));
        return $namespace . '\\' . ucfirst($shortName) . 'Builder';
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

        return 'ExternalApi\\' . ucfirst($shortName) . 'Gateway';
    }


    public static function getMethodName(string $name, string $prefix = 'get'): string
    {
        $name = str_replace('_', '', ucwords($name, '_'));
        if(!$prefix){
            $name = lcfirst($name);
        }

        return $prefix . $name;
    }


    public static function getEntityClassName(string $shortName, string $gateway): string
    {
        if (is_subclass_of($shortName, Entity::class, true)) {
            return $shortName;
        }

        $namespace = substr($gateway, 0, strrpos($gateway, '\\'));
        return $namespace . '\\Entities\\' . ucfirst($shortName);
    }


    public static function getEntityName(string|Entity $entity): string
    {
        $entity = is_object($entity) ? (new ReflectionClass($entity))->getName() : $entity;
        $words = explode('\\', $entity);
        $entity = array_pop($words);

        return strtolower($entity);
    }
}