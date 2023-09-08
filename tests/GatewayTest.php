<?php

namespace ExternalApi\Tests;

use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Common\Helper;
use ExternalApi\Common\Request;
use ExternalApi\Common\Response;
use ExternalApi\Contracts\RequestBuilderInterface;
use ExternalApi\Exceptions\GatewayException;
use ExternalApi\ExternalApi;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response as BaseResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;


class GatewayTest extends TestCase
{
    public function test_get_gateway_class_name_by_name()
    {
        $class = Helper::getGatewayClassName('bitrix24');
        $this->assertEquals(Gateway::class, $class);
    }


    public function test_get_gateway_class_name_by_class()
    {
        $class = Helper::getGatewayClassName(Gateway::class);
        $this->assertEquals(Gateway::class, $class);
    }


    public function test_create_gateway_bitrix24(): Gateway
    {
        $gateway = ExternalApi::create('bitrix24');
        $this->assertEquals('bitrix24', $gateway->getName());

        return $gateway;
    }

    /**
     * @depends test_create_gateway_bitrix24
     */
    public function test_can_not_call_webhook_endpoint(Gateway $gateway)
    {
        $this->expectException(GatewayException::class);
        $gateway->getWebhookEndpoint();
    }

    /**
     * @depends test_create_gateway_bitrix24
     * @throws \ExternalApi\Exceptions\GatewayException
     */
    public function test_call_webhook_endpoint(Gateway $gateway)
    {
        $url = 'https://test.loc';

        $gateway->setWebhookEndpoint($url);
        $this->assertEquals($url, $gateway->getWebhookEndpoint());
    }

    /**
     * @depends test_create_gateway_bitrix24
     */
    public function test_can_not_set_unknown_method(Gateway $gateway)
    {
        $this->expectException(GatewayException::class);

        $gateway->setMethod('BAD');
    }

    /**
     * @depends test_create_gateway_bitrix24
     */
    public function test_set_valid_method(Gateway $gateway)
    {
        $this->expectNotToPerformAssertions();

        $gateway->setMethod('GET');
        $gateway->setMethod('PATCH');
        $gateway->setMethod('PUT');
        $gateway->setMethod('DELETE');
        $gateway->setMethod('POST');
    }

    /**
     * @depends test_create_gateway_bitrix24
     */
    public function test_create_request_builder(Gateway $gateway)
    {
        $builder = $gateway->createRequestBuilder('deal');

        $this->assertInstanceOf(RequestBuilderInterface::class, $builder);
    }

    /**
     * @dataProvider additionCallProvider
     */
    public function test_gateway_call_api($requestData, $parameters, $expect)
    {
        $url = 'https://test.loc';

        $response = $this->createStub(BaseResponse::class);
        $response->method('getStatusCode')
            ->willReturn(200);

        $client = $this->createStub(Client::class);
        $client->method('request')
            ->with($this->stringContains($expect['type']),
                $this->stringContains($expect['url']),
                $this->equalTo($expect['options'])
            )
            ->willReturn($response);

        $gateway = new Gateway($client);
        $gateway->setWebhookEndpoint($url);

        if (isset($parameters['agent'])) {
            $gateway->setHeader('User-Agent', $parameters['agent']);
        }
        if (isset($parameters['verify'])) {
            $gateway->setVerify($parameters['verify']);
        }
        if (isset($parameters['query']) && is_array($parameters['query'])) {
            foreach ($parameters['query'] as $key => $value) {
                $gateway->setQueryValue($key, $value);
            }
        }

        $request = new Request($requestData);

        $response = $gateway->call($request);
        $this->assertInstanceOf(Response::class, $response);
    }


    public function additionCallProvider(): array
    {
        return [
            [
                [
                    'method' => 'update',
                    'data' => ['test']
                ],
                [
                    'agent' => 'Test Api'
                ],
                [
                    'type' => 'POST',
                    'url' => 'https://test.loc/update',
                    'options' => [
                        'headers' => [
                            'User-Agent' => 'Test Api',
                        ],
                        'body' => json_encode(['test']),
                        'verify' => false
                    ],
                ]
            ],
            [
                [],
                [
                    'verify' => 'test',
                ],
                [
                    'type' => 'GET',
                    'url' => 'https://test.loc',
                    'options' => [
                        'headers' => [
                            'User-Agent' => 'External webhook client',
                        ],
                        'verify' => 'test'
                    ],
                ]
            ],
            [
                [
                    'headers' => ['test' => 1],
                    'query' => ['key1' => 'test1'],
                ],
                [
                    'query' => ['key2' => 'test2'],
                ],
                [
                    'type' => 'GET',
                    'url' => 'https://test.loc',
                    'options' => [
                        'query' => [
                            'key1' => 'test1',
                            'key2' => 'test2'
                        ],
                        'headers' => [
                            'User-Agent' => 'External webhook client',
                            'test' => 1
                        ],
                        'verify' => false
                    ],
                ]
            ],
        ];
    }
}
