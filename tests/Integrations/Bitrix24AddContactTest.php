<?php

namespace ExternalApi\Tests\Integrations;

use ExternalApi\Bitrix24\Responses\ContactIdResponse;
use ExternalApi\Bitrix24\Gateway;
use ExternalApi\ExternalApi;
use PHPUnit\Framework\TestCase;


class Bitrix24AddContactTest extends TestCase
{
    private array $config = [];

    private Gateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();

        if (file_exists(__DIR__ . '/config.php')) {
            $this->config = include(__DIR__ . '/config.php');
        }

        $this->gateway = ExternalApi::create('bitrix24');
        $this->gateway->setWebhookEndpoint($this->config['bitrix24_webhook_endpoint']);
    }


    public function test_bitrix24_find_contacts_by_contact()
    {
        $contact = $this->gateway
            ->createEntity('contact', [
                'phone' => ['+7 900 100-10-01', '89001001002'],
                'email' => 'test1@test.ru',
                'first_name' => 'Иван',
                'last_name' => 'Тестов'
            ]);

        $builder = $this->gateway
            ->createRequestBuilder('contact')
            ->method('create')
            ->setFields($contact->getFields())
            ->setNotify(false);

        $response = $this->gateway->call($builder);

        $this->assertInstanceOf(ContactIdResponse::class, $response);
        $resource = $response->getResource();
        $this->assertGreaterThan(19600, $resource->id);
    }
}
