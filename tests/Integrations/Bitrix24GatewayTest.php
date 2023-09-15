<?php

namespace ExternalApi\Tests\Integrations;

use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Common\Builder;
use ExternalApi\Bitrix24\Responses\Response;
use ExternalApi\ExternalApi;
use PHPUnit\Framework\TestCase;


class Bitrix24GatewayTest extends TestCase
{
    private array $config = [];

    private Gateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();

        if (file_exists( __DIR__ .'/config.php')) {
            $this->config = include(__DIR__.'/config.php');
        }

        $this->gateway = ExternalApi::create('bitrix24');
        $this->gateway->setWebhookEndpoint($this->config['bitrix24_webhook_endpoint']);
    }


    public function test_bitrix24_call()
    {
        $builder = (new Builder())->method('scope');

        $response = $this->gateway->call($builder->build());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertFalse($response->hasErrors());
        $this->assertContains('crm', $response->getResult());
    }


    public function test_bitrix24_max_2_call_per_second()
    {
        $builder = (new Builder())->method('scope')->build();

        $time_start = microtime(true);
        $this->gateway->call($builder);
        $this->gateway->call($builder);
        $time_end = microtime(true);

        $this->assertGreaterThan(1, $time_end-$time_start);

        $this->gateway->call($builder);
        $time_end = microtime(true);
        $this->assertGreaterThan(1.5, $time_end-$time_start);
    }

}
