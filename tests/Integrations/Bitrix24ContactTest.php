<?php

namespace ExternalApi\Tests\Integrations;

use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Bitrix24\Responses\ContactIdResponse;
use ExternalApi\Bitrix24\Responses\ContactResponse;
use ExternalApi\Common\Response;
use ExternalApi\Exceptions\BuilderException;
use ExternalApi\ExternalApi;
use PHPUnit\Framework\TestCase;


class Bitrix24ContactTest extends TestCase
{
    private array $config = [];

    private Gateway $gateway;

    private array $demoFields = [
        'phone' => ['+7 900 100-10-01', '89001001002'],
        'email' => 'test1@test.ru',
        'first_name' => 'Иван',
        'last_name' => 'Тестов'
    ];


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


    public function test_bitrix24_can_not_update_contact_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('contact')
            ->method('update')
            ->call();
    }


    public function test_bitrix24_can_not_delete_contact_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('contact')
            ->method('delete')
            ->build();
    }


    public function test_bitrix24_add_contact()
    {
        $contact = $this->gateway
            ->createEntity('contact', $this->demoFields);

        $builder = $this->gateway
            ->createRequestBuilder('contact')
            ->method('create')
            ->setFields($contact->getFields())
            ->setNotify(false);

        $response = $this->gateway->call($builder);

        $this->assertInstanceOf(ContactIdResponse::class, $response);
        $resource = $response->getResource();
        $this->assertGreaterThan(19600, $resource->id);

        return $resource->id;
    }

    /**
     * @depends test_bitrix24_add_contact
     */
    public function test_bitrix24_update_contact($id)
    {
        $contact = $this->gateway
            ->createEntity('contact', [
                'comments' => 'test update',
                'last_name' => $this->demoFields['last_name'] . 'в'
            ]);

        $response = $this->gateway
            ->createRequestBuilder('contact')
            ->method('update')
            ->setFields($contact->getFields())
            ->setId($id)
            ->call();

        $this->assertInstanceOf(ContactIdResponse::class, $response);
        $this->assertEquals($id, $response->getResource()->id);
    }

    /**
     * @depends test_bitrix24_add_contact
     */
    public function test_bitrix24_get_contact_by_id($id)
    {
        $response = $this->gateway
            ->createRequestBuilder('contact')
            ->method('get')
            ->setId($id)
            ->call();

        $this->assertInstanceOf(ContactResponse::class, $response);
        $resource = $response->getResource();
        $this->assertEquals($id, $resource->id);
        $this->assertEquals($this->demoFields['first_name'], $resource->first_name);
        $this->assertEquals($this->demoFields['last_name'] . 'в', $resource->last_name);
        $this->assertEquals($this->demoFields['phone'], array_values($resource->phone));
        $this->assertEquals([$this->demoFields['email']], array_values($resource->email));
        $this->assertEquals('test update', $resource->comments);
    }

    /**
     * @depends test_bitrix24_add_contact
     */
    public function test_bitrix24_delete_contact($id)
    {
        $response = $this->gateway
            ->createRequestBuilder('contact')
            ->method('delete')
            ->setId($id)
            ->call();

        $this->assertInstanceOf(Response::class, $response);
    }
}
