<?php

namespace ExternalApi\Tests\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response as BaseResponse;


trait AssertGateway
{
    protected function getAssertGatewayStub($gateway, $expectUrl, $expectOptions, $expectType = 'POST')
    {
        $response = $this->createStub(BaseResponse::class);
        $response->method('getStatusCode')
            ->willReturn(200);

        $client = $this->createStub(Client::class);
        $client->method('request')
            ->with($this->stringContains($expectType),
                $this->stringContains($expectUrl),
                $this->callback(
                    function ($option) use($expectOptions){
                        $expect = print_r($expectOptions, true);
                        $result = print_r($option, true);
                        $this->assertEquals($expectOptions, $option, $result."\n\nExpected\n".$expect);
                        return true;
                    }
                )
            )
            ->willReturn($response);

        return (new $gateway($client))->setThrottle(false);
    }
}