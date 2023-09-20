<?php

namespace ExternalApi\Tests\Integrations;

use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Bitrix24\Responses\ProductIdResponse;
use ExternalApi\Bitrix24\Responses\ProductListResponse;
use ExternalApi\Bitrix24\Responses\ProductResponse;
use ExternalApi\Common\Response;
use ExternalApi\Contracts\FilterInterface;
use ExternalApi\Exceptions\BuilderException;
use ExternalApi\ExternalApi;
use PHPUnit\Framework\TestCase;


class Bitrix24ProductTest extends TestCase
{
    private array $config = [];

    private Gateway $gateway;

    private array $demoFields = [
        'price' => 10000,
        'article' => '1234567890'
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


    public function test_bitrix24_add_product()
    {
        $product = $this->gateway
            ->createEntity('product', ['name' => 'Тест 1'])
            ->setSettingFields($this->config['product']['fieldCodes'])
            ->setFields($this->demoFields);

        $response = $this->gateway
            ->createRequestBuilder('product')
            ->method('create')
            ->setFields($product->getFields())
            ->call();

        $this->assertInstanceOf(ProductIdResponse::class, $response);
        $resource = $response->getResource();
        $this->assertGreaterThan(200, $resource->id);

        return $resource->id;
    }

    /**
     * @depends test_bitrix24_add_product
     */
    public function test_bitrix24_list_product($id)
    {
        $response = $this->gateway
            ->createRequestBuilder('product')
            ->select('id','name', 'price')
            ->where(function (FilterInterface $filter) {
                $filter->equal($this->config['product']['fieldCodes']['article'], $this->demoFields['article']);
                return $filter;
            })
            ->call();

        $this->assertInstanceOf(ProductListResponse::class, $response);
        $resource = $response->getResource();
        $item = $resource->getItems()[0];
        $this->assertEquals($id, $item->id);
    }

    /**
     * @depends test_bitrix24_add_product
     */
    public function test_bitrix24_update_product($id)
    {
        $product = $this->gateway
            ->createEntity('product', ['name' => 'Тест 1 новинка']);

        $response = $this->gateway
            ->createRequestBuilder('product')
            ->method('update')
            ->setFields($product->getFields())
            ->setId($id)
            ->call();

        $this->assertInstanceOf(ProductIdResponse::class, $response);
        $resource = $response->getResource();
        $this->assertEquals($id, $resource->id);
    }

    /**
     * @depends test_bitrix24_add_product
     */
    public function test_bitrix24_get_product($id)
    {
        $response = $this->gateway
            ->createRequestBuilder('product')
            ->method('get')
            ->setId($id)
            ->call();

        $this->assertInstanceOf(ProductResponse::class, $response);
        $resource = $response->getResource();
        $this->assertEquals($id, $resource->id);
        $this->assertEquals('Тест 1 новинка', $resource->name);

        $resource->setSettingFields($this->config['product']['fieldCodes']);

        foreach ($this->demoFields as $key => $value) {
            $this->assertEquals($value, $resource->{$key});
        }
    }

    /**
     * @depends test_bitrix24_add_product
     */
    public function test_bitrix24_delete_product($id)
    {
        $response = $this->gateway
            ->createRequestBuilder('product')
            ->method('delete')
            ->setId($id)
            ->call();

        $this->assertInstanceOf(Response::class, $response);
    }


    public function test_bitrix24_can_not_get_product_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('product')
            ->method('get')
            ->call();
    }


    public function test_bitrix24_can_not_update_product_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('product')
            ->method('update')
            ->call();
    }


    public function test_bitrix24_can_not_delete_product_without_id()
    {
        $this->expectException(BuilderException::class);

        $this->gateway
            ->createRequestBuilder('product')
            ->method('delete')
            ->build();
    }
}
