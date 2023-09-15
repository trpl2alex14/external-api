<?php

namespace ExternalApi\Tests\Features;

use ExternalApi\Bitrix24\Contact;
use ExternalApi\Bitrix24\ContactBuilder;
use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Common\Helper;
use PHPUnit\Framework\TestCase;


class HelperTest extends TestCase
{
    public function test_get_gateway_class_name_by_name()
    {
        $name = Helper::getGatewayClassName('bitrix24');
        $this->assertEquals(Gateway::class, $name);
    }


    public function test_get_gateway_class_name_by_class()
    {
        $class = Helper::getGatewayClassName(Gateway::class);
        $this->assertEquals(Gateway::class, $class);
    }


    public function test_get_request_class_name_by_name_and_gateway_instance()
    {
        $name = Helper::getRequestClassName('contact', new Gateway());
        $this->assertEquals(ContactBuilder::class, $name);
    }


    public function test_get_request_class_name_by_name()
    {
        $name = Helper::getRequestClassName('contact', Gateway::class);
        $this->assertEquals(ContactBuilder::class, $name);
    }


    public function test_get_method_name()
    {
        $name = Helper::getMethodName('first_name');
        $this->assertEquals('getFirstName', $name);

        $name = Helper::getMethodName('first_name', 'set');
        $this->assertEquals('setFirstName', $name);

        $name = Helper::getMethodName('name' );
        $this->assertEquals('getName', $name);

        $name = Helper::getMethodName('name_value', '');
        $this->assertEquals('nameValue', $name);
    }


    public function test_get_entity_class_name_by_name()
    {
        $name = Helper::getEntityClassName('contact', Gateway::class);
        $this->assertEquals(Contact::class, $name);
    }
}
