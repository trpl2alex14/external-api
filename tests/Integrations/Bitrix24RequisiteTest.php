<?php

namespace ExternalApi\Tests\Integrations;

use ExternalApi\Bitrix24\Entities\Contact;
use ExternalApi\Bitrix24\Entities\Requisite;
use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Bitrix24\Responses\ContactWithRequisiteResponse;
use ExternalApi\Bitrix24\Responses\RequisiteResponse;
use ExternalApi\Bitrix24\Responses\Response;
use ExternalApi\Exceptions\BuilderException;
use ExternalApi\ExternalApi;
use PHPUnit\Framework\TestCase;


class Bitrix24RequisiteTest extends TestCase
{
    private array $config = [];

    private Gateway $gateway;

    private array $demoFields = [
        'phone' => ['+7 900 100-10-02'],
        'email' => 'test2@test.ru',
        'first_name' => 'Петр',
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


    public function test_bitrix24_can_not_get_requisite_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('requisite')
            ->method('get')
            ->call();
    }


    public function test_bitrix24_can_not_update_requisite_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('requisite')
            ->method('update')
            ->call();
    }


    public function test_bitrix24_can_not_delete_requisite_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('requisite')
            ->method('delete')
            ->build();
    }


    public function test_bitrix24_add_contact_with_requisite(): array
    {
        $contact = $this->gateway
            ->createEntity('contact', $this->demoFields);

        $response = $this->gateway
            ->createRequestBuilder('contact')
            ->method('create')
            ->setFields($contact->getFields())
            ->addRequisite()
            ->call();

        $this->assertInstanceOf(ContactWithRequisiteResponse::class, $response);

        $resource = $response->getResource();
        $this->assertInstanceOf(Contact::class, $resource);
        $this->assertGreaterThan(19600, $resource->id);

        $requisite = $response->getRequisite();
        $this->assertInstanceOf(Requisite::class, $requisite);
        $this->assertGreaterThan(7964, $requisite->id);

        return [$resource->id, $requisite->id];
    }

    /**
     * @depends test_bitrix24_add_contact_with_requisite
     */
    public function test_bitrix24_update_requisite($resource)
    {
        list($contactId, $requisiteId) = $resource;

        $requisite = $this->gateway
            ->createEntity('requisite', [
                'first_name' => $this->demoFields['first_name'] . 'a',
                'last_name' => $this->demoFields['last_name'] . 'в',
                'second_name' => 'УДАЛИТЬ',
            ]);

        $response = $this->gateway
            ->createRequestBuilder('requisite')
            ->method('update')
            ->setFields($requisite->getFields())
            ->setId($requisiteId)
            ->call();

        $this->assertInstanceOf(RequisiteResponse::class, $response);
        $this->assertEquals($requisiteId, $response->getResource()->id);
    }

    /**
     * @depends test_bitrix24_add_contact_with_requisite
     */
    public function test_bitrix24_get_requisite_by_id($resource)
    {
        list($contactId, $requisiteId) = $resource;

        $response = $this->gateway
            ->createRequestBuilder('requisite')
            ->method('get')
            ->setId($requisiteId)
            ->call();

        $this->assertInstanceOf(RequisiteResponse::class, $response);
        $resource = $response->getResource();
        $this->assertEquals($requisiteId, $resource->id);
        $this->assertEquals($this->demoFields['first_name'] . 'a', $resource->first_name);
        $this->assertEquals($this->demoFields['last_name'] . 'в', $resource->last_name);
        $this->assertEquals('УДАЛИТЬ', $resource->second_name);
        $this->assertEquals($contactId, $resource->entity_id);
    }

    /**
     * @depends test_bitrix24_add_contact_with_requisite
     */
    public function test_bitrix24_delete_contact($resource)
    {
        list($contactId, $requisiteId) = $resource;
        $response = $this->gateway
            ->createRequestBuilder('requisite')
            ->method('delete')
            ->setId($requisiteId)
            ->call();

        $this->assertInstanceOf(Response::class, $response);

        $response = $this->gateway
            ->createRequestBuilder('contact')
            ->method('delete')
            ->setId($contactId)
            ->call();

        $this->assertInstanceOf(Response::class, $response);
    }
}
