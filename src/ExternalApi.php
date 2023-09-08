<?php

namespace ExternalApi;

use ExternalApi\Common\Gateway;
use ExternalApi\Common\GatewayFactory;
use ExternalApi\Contracts\GatewayInterface;


/**
 * @method static Gateway create(string $name)
 */
class ExternalApi
{
    private static ?GatewayFactory $factory = null;


    public static function getFactory(): GatewayFactory
    {
        if (is_null(self::$factory)) {
            self::$factory = new GatewayFactory;
        }

        return self::$factory;
    }


    public static function setFactory(GatewayFactory $factory = null)
    {
        self::$factory = $factory;
    }

    /**
     * Example:
     *
     * <code>
     *   $gateway = ExternalApi::create('bitrix24');
     * </code>
     *
     * @param string $method     The factory method to invoke.
     * @param array  $parameters Parameters passed to the factory method.
     *
     * @return GatewayInterface
     */
    public static function __callStatic(string $method, array $parameters)
    {
        $factory = self::getFactory();

        return call_user_func_array(array($factory, $method), $parameters);
    }
}