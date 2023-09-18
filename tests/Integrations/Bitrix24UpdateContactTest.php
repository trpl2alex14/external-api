<?php

namespace ExternalApi\Tests\Integrations;

use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Bitrix24\Responses\ContactIdResponse;
use ExternalApi\Exceptions\BuilderException;
use ExternalApi\ExternalApi;
use PHPUnit\Framework\TestCase;


class Bitrix24UpdateContactTest extends TestCase
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


    public function test_bitrix24_can_not_update_contact_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('contact')
            ->method('update')
            ->call();
    }


    public function test_bitrix24_update_contact()
    {
        $contact = $this->gateway
            ->createRequestBuilder('contact')
            ->method('get')
            ->setId(19604)
            ->call()
            ->getResource();

        $comments = $contact->comments . 'test update';

        $updateContact = $this->gateway
            ->createEntity('contact')
            ->setField('comments', $comments);

        $response = $this->gateway
            ->createRequestBuilder('contact')
            ->method('update')
            ->setFields($updateContact->getFields())
            ->setId(19604)
            ->call();

        $this->assertInstanceOf(ContactIdResponse::class, $response);
        $this->assertEquals(19604, $response->getResource()->id);

        $contact = $this->gateway
            ->createRequestBuilder('contact')
            ->method('get')
            ->setId(19604)
            ->call()
            ->getResource();

        $this->assertEquals($comments, $contact->comments);
    }
}
