<?php

namespace ExternalApi\Tests\Integrations;

use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Bitrix24\Responses\LeadResponse;
use ExternalApi\Common\Response;
use ExternalApi\Exceptions\BuilderException;
use ExternalApi\ExternalApi;
use PHPUnit\Framework\TestCase;


class Bitrix24LeadTest extends TestCase
{
    private array $config = [];

    private Gateway $gateway;

    private array $demoFields = [
        'title' => '**Тест API** сайта',
        'phone' => ['+7 900 100-12-02', '89001001003'],
        'email' => 'testapi1@test.ru',
        'name' => 'Иван Тестов',
        'comment' => 'комментарий к лиду',
        'source_id' => '10'
    ];

    private array $demoExpectedFields = [
        'title' => '**Тест API** сайта',
        'phone' => ['79001001202', '79001001003'],
        'email' => 'testapi1@test.ru',
        'name' => 'Иванапи Лидтестов',
        'comment' => 'комментарий к лиду'
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


    public function test_bitrix24_can_not_get_lead_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('lead')
            ->method('get')
            ->call();
    }


    public function test_bitrix24_can_not_update_lead_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('lead')
            ->method('update')
            ->call();
    }


    public function test_bitrix24_can_not_delete_lead_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('lead')
            ->method('delete')
            ->build();
    }


    public function test_bitrix24_add_lead()
    {
        $lead = $this->gateway
            ->createEntity('lead', $this->demoFields);

        $builder = $this->gateway
            ->createRequestBuilder('lead')
            ->method('create')
            ->setFields($lead->getFields())
            ->setNotify(false);

        $response = $this->gateway->call($builder);

        $this->assertInstanceOf(LeadResponse::class, $response);
        $resource = $response->getResource();
        $this->assertGreaterThan(19600, $resource->id);

        return $resource->id;
    }

    /**
     * @depends test_bitrix24_add_lead
     */
    public function test_bitrix24_update_lead($id)
    {
        $lead = $this->gateway
            ->createEntity('lead', [
                'comments' => 'test update',
                'name' => $this->demoFields['name'] . 'в'
            ]);

        $response = $this->gateway
            ->createRequestBuilder('lead')
            ->method('update')
            ->setFields($lead->getFields())
            ->setId($id)
            ->call();

        $this->assertInstanceOf(LeadResponse::class, $response);
        $this->assertEquals($id, $response->getResource()->id);
    }

    /**
     * @depends test_bitrix24_add_lead
     */
    public function test_bitrix24_get_lead_by_id($id)
    {
        $response = $this->gateway
            ->createRequestBuilder('lead')
            ->method('get')
            ->setId($id)
            ->call();

        $this->assertInstanceOf(LeadResponse::class, $response);
        $resource = $response->getResource();
        $this->assertEquals($id, $resource->id);
        $this->assertEquals($this->demoFields['name'] . 'в', $resource->name);
        $this->assertEquals($this->demoExpectedFields['phone'], array_values($resource->phone));
        $this->assertEquals([$this->demoFields['email']], array_values($resource->email));
        $this->assertEquals('test update', $resource->comments);
    }

    /**
     * @depends test_bitrix24_add_lead
     */
    public function test_bitrix24_delete_lead($id)
    {
        $response = $this->gateway
            ->createRequestBuilder('lead')
            ->method('delete')
            ->setId($id)
            ->call();

        $this->assertInstanceOf(Response::class, $response);
    }
}
