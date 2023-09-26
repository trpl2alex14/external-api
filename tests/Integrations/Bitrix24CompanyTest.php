<?php

namespace ExternalApi\Tests\Integrations;

use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Bitrix24\Responses\CompanyResponse;
use ExternalApi\Bitrix24\Responses\CompanyWithRequisiteResponse;
use ExternalApi\Common\Response;
use ExternalApi\Exceptions\BuilderException;
use ExternalApi\ExternalApi;
use PHPUnit\Framework\TestCase;


class Bitrix24CompanyTest extends TestCase
{
    private array $config = [];

    private Gateway $gateway;

    private array $demoFields = [
        'title' => 'ООО "ТЕСТ РиК',
        'inn' => '123123123012'
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


    public function test_bitrix24_can_not_get_company_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('company')
            ->method('get')
            ->call();
    }


    public function test_bitrix24_can_not_update_company_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('company')
            ->method('update')
            ->call();
    }


    public function test_bitrix24_can_not_delete_company_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('company')
            ->method('delete')
            ->build();
    }


    public function test_bitrix24_add_company()
    {
        $contact = $this->gateway
            ->createEntity('company', $this->demoFields);

        $response = $this->gateway
            ->createRequestBuilder('company')
            ->method('create')
            ->setFields($contact->getFields())
            ->setNotify(false)
            ->call();

        $this->assertInstanceOf(CompanyResponse::class, $response);
        $resource = $response->getResource();
        $this->assertGreaterThan(2700, $resource->id);

        return $resource->id;
    }

    /**
     * @depends test_bitrix24_add_company
     */
    public function test_bitrix24_update_company($id)
    {
        $contact = $this->gateway
            ->createEntity('company', [
                'comments' => 'test update',
                'title' => $this->demoFields['title'] . ' тест'
            ]);

        $response = $this->gateway
            ->createRequestBuilder('company')
            ->method('update')
            ->setFields($contact->getFields())
            ->setId($id)
            ->call();

        $this->assertInstanceOf(CompanyResponse::class, $response);
        $this->assertEquals($id, $response->getResource()->id);
    }

    /**
     * @depends test_bitrix24_add_company
     */
    public function test_bitrix24_get_company_by_id($id)
    {
        $response = $this->gateway
            ->createRequestBuilder('company')
            ->method('get')
            ->setId($id)
            ->call();

        $this->assertInstanceOf(CompanyResponse::class, $response);
        $resource = $response->getResource();
        $this->assertEquals($id, $resource->id);
        $this->assertEquals($this->demoFields['title'] . ' тест', $resource->title);
        $this->assertEquals('test update', $resource->comments);
    }

    /**
     * @depends test_bitrix24_add_company
     */
    public function test_bitrix24_delete_company($id)
    {
        $response = $this->gateway
            ->createRequestBuilder('company')
            ->method('delete')
            ->setId($id)
            ->call();

        $this->assertInstanceOf(Response::class, $response);
    }


    public function test_bitrix24_add_company_with_requisite()
    {
        $contact = $this->gateway
            ->createEntity('company', $this->demoFields);

        $response = $this->gateway
            ->createRequestBuilder('company')
            ->method('create')
            ->setFields($contact->getFields())
            ->addRequisite()
            ->call();

        $this->assertInstanceOf(CompanyWithRequisiteResponse::class, $response);
        $resource = $response->getResource();
        $this->assertGreaterThan(2700, $resource->id);

        return $resource->id;
    }

    /**
     * @depends test_bitrix24_add_company_with_requisite
     */
    public function test_bitrix24_delete_company_with_requisite($id)
    {
        $response = $this->gateway
            ->createRequestBuilder('company')
            ->method('delete')
            ->setId($id)
            ->call();

        $this->assertInstanceOf(Response::class, $response);
    }
}
