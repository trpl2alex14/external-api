<?php

namespace ExternalApi\Tests\Features\Bitrix24;

use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Bitrix24\TriggerBuilder;
use ExternalApi\Common\Response;
use ExternalApi\Tests\Traits\AssertGateway;
use PHPUnit\Framework\TestCase;


class TriggerBuilderTest extends TestCase
{
    use AssertGateway;

    public function test_trigger_builder()
    {
        $url = 'https://test.loc';
        $code = 'test1';

        $gateway = $this->getAssertGatewayStub(Gateway::class,
            'https://test.loc/crm.automation.trigger',
            [
                'headers' => [
                    'User-Agent' => 'External webhook client',
                ],
                'query' => [
                    'code' => $code,
                    'target' => 'DEAL_155'
                ],
                'verify' => false,
            ],
            'GET'
        );
        $gateway->setWebhookEndpoint($url);

        $builder = new TriggerBuilder();
        $builder->setParameters(['code' => $code])
            ->setParameter('target', 'deal')
            ->setId(155);

        $response = $gateway->call($builder);
        $this->assertInstanceOf(Response::class, $response);
    }

}
