<?php

namespace ExternalApi\Tests\Features\Bitrix24;

use ExternalApi\Bitrix24\Entities\Contact;
use PHPUnit\Framework\TestCase;


class ContactTest extends TestCase
{

    public function test_contact_entity()
    {
        $contact = new Contact([
            'id' => 19604,
            'phone' => ['+7 900 123-12-12', '+7 900 123-12-10'],
            'email' => 'test@test.ru',
            'first_name' => 'алексей',
            'last_name' => 'тест'
        ]);

        $this->assertEquals(['79001231212', '79001231210'], $contact->getPhone());
        $this->assertEquals(['test@test.ru'], $contact->getEmail());
        $this->assertEquals([['VALUE' => 'test@test.ru']], $contact->getRaw()['EMAIL']);
        $this->assertEquals(['VALUE' => '79001231212'], $contact->getRaw()['PHONE'][0]);
        $this->assertEquals('алексей', $contact->getRaw()['NAME']);
        $this->assertEquals('тест', $contact->getRaw()['LAST_NAME']);
        $this->assertEquals('алексей', $contact->getFirstName());
        $this->assertEquals('тест', $contact->getLastName());
    }
}
