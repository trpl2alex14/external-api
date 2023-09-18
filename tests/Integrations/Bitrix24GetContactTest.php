<?php

namespace ExternalApi\Tests\Integrations;

use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Bitrix24\Responses\ContactResponse;
use ExternalApi\Exceptions\BuilderException;
use ExternalApi\ExternalApi;
use PHPUnit\Framework\TestCase;


class Bitrix24GetContactTest extends TestCase
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


    public function test_bitrix24_can_not_get_contact_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('contact')
            ->method('get')
            ->call();
    }


    public function test_bitrix24_get_contact_by_id()
    {
        $response = $this->gateway
            ->createRequestBuilder('contact')
            ->method('get')
            ->setId(19604)
            ->call();

        $this->assertInstanceOf(ContactResponse::class, $response);
        $resource = $response->getResource();
        $this->assertEquals(19604, $resource->id);
        $this->assertEquals('Алексей', $resource->first_name);
        $this->assertEquals('Тест', $resource->last_name);
        $this->assertEquals([122048 => '+79001231212'], $resource->phone);
        $this->assertEquals([122050 => 'test@test.ru'], $resource->email);
    }
}
