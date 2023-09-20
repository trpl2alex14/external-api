<?php

namespace ExternalApi\Tests\Integrations;

use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Bitrix24\Responses\DealBatchResponse;
use ExternalApi\Bitrix24\Responses\DealIdResponse;
use ExternalApi\Bitrix24\Responses\DealResponse;
use ExternalApi\Common\Response;
use ExternalApi\Exceptions\BuilderException;
use ExternalApi\ExternalApi;
use PHPUnit\Framework\TestCase;


class Bitrix24DealTest extends TestCase
{
    private array $config = [];

    private Gateway $gateway;

    private array $demoFields = [
        'city' => 'Челябинск',
        'comments' => 'test',
        'order_id' => 12345,
        'payment_type' => '74',
        'delivery_type' => '94',
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


    public function test_bitrix24_add_deal()
    {
        $deal = $this->gateway
            ->createEntity('deal', ['title' => 'ТЕСТ - ИМ экспорт'])
            ->setSettingFields($this->config['deal']['fieldCodes'])
            ->setFields($this->config['deal']['defaultFields'])
            ->setFields($this->demoFields);

        $contact = $this->gateway
            ->createEntity('contact', [
                'phone' => ['+7 900 123-12-12', '+7 900 123-12-10'],
                'email' => 'test@test.ru',
                'first_name' => 'алексей',
                'last_name' => 'тест'
            ]);

        $clients = $this->gateway
            ->createRequestBuilder('contact')
            ->method('findBy')
            ->byContact($contact)
            ->call()
            ->getResource()
            ->getItems();

        $items = [
            $this->gateway->createEntity('productRow', [
                    'name' => 'Тест 1',
                    'price' => 10000,
                    'quantity' => 5,
                    'discount' => 1000,
                    'tax' => 10,
                ]),
            $this->gateway->createEntity('productRow', [
                'name' => 'Тест 2',
                'price' => 1000,
                'quantity' => 1,
                'discount' => 10,
                'tax' => 20,
                'tax_included' => 'N',
            ]),
            $this->gateway->createEntity('productRow', [
                'name' => 'Тест 3',
                'price' => 100,
                'quantity' => 2,
                'discount' => 0,
                'tax' => 0,
                'measure_code' => 112
            ]),
        ];

        $response = $this->gateway
            ->createRequestBuilder('deal')
            ->method('create')
            ->setFields($deal->getFields())
            ->setClients($clients)
            ->setCompany(524)  //Skayber
            ->setItems($items)
            ->call();

        $this->assertInstanceOf(DealBatchResponse::class, $response);
        $resource = $response->getResource();
        $this->assertGreaterThan(27300, $resource->id);

        return $resource->id;
    }

    /**
     * @depends test_bitrix24_add_deal
     */
    public function test_bitrix24_update_deal($id)
    {
        $response = $this->gateway
            ->createRequestBuilder('deal')
            ->method('update')
            ->setFields(['TITLE' => 'ТЕСТ - ИМ экспорт обновление'])
            ->setId($id)
            ->call();

        $this->assertInstanceOf(DealIdResponse::class, $response);
        $resource = $response->getResource();
        $this->assertEquals($id, $resource->id);
    }

    /**
     * @depends test_bitrix24_add_deal
     */
    public function test_bitrix24_get_deal($id)
    {
        $response = $this->gateway
            ->createRequestBuilder('deal')
            ->method('get')
            ->setId($id)
            ->call();

        $this->assertInstanceOf(DealResponse::class, $response);
        $resource = $response->getResource();
        $this->assertEquals($id, $resource->id);
        $this->assertEquals('ТЕСТ - ИМ экспорт обновление', $resource->title);

        $resource->setSettingFields($this->config['deal']['fieldCodes']);

        foreach ($this->demoFields as $key => $value) {
            $this->assertEquals($value, $resource->getField($key));
        }
    }

    /**
     * @depends test_bitrix24_add_deal
     */
    public function test_bitrix24_delete_deal($id)
    {
        $response = $this->gateway
            ->createRequestBuilder('deal')
            ->method('delete')
            ->setId($id)
            ->call();

        $this->assertInstanceOf(Response::class, $response);
    }


    public function test_bitrix24_can_not_get_deal_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('deal')
            ->method('get')
            ->call();
    }


    public function test_bitrix24_can_not_update_deal_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('deal')
            ->method('update')
            ->call();
    }


    public function test_bitrix24_can_not_delete_deal_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('deal')
            ->method('delete')
            ->build();
    }
}
