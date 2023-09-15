<?php

namespace ExternalApi\Tests\Features\Bitrix24;

use ExternalApi\Bitrix24\BatchBuilder;
use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Common\Builder;
use ExternalApi\Common\Response;
use ExternalApi\Exceptions\BuilderException;
use ExternalApi\Tests\Traits\AssertGateway;
use PHPUnit\Framework\TestCase;


class BatchBuilderBitrix24Test extends TestCase
{
    use AssertGateway;


    public function test_can_not_call_empty_batch()
    {
        $this->expectException(BuilderException::class);

        $builder = new BatchBuilder();

        $builder->build();
    }


    public function test_can_not_take_input_properties_from_unknown_command()
    {
        $this->expectException(BuilderException::class);

        $builder = new BatchBuilder();
        $builder->setCommand((new Builder())->method('test.method'), 'new');
        $builder->takeInputFrom('unknown', 'new', 'id', 'id');
    }


    public function test_can_not_set_input_properties_to_unknown_command()
    {
        $this->expectException(BuilderException::class);

        $builder = new BatchBuilder();
        $builder->setCommand((new Builder())->method('test.method'), 'first');
        $builder->takeInputFrom('first', 'unknown', 'id', 'id');
    }

    /**
     * @dataProvider additionCallProvider
     */
    public function test_batch_builder($halt, $requestData, $expect)
    {
        $url = 'https://test.loc';

        $gateway = $this->getAssertGatewayStub(Gateway::class,
            'https://test.loc/batch',
            [
                'headers' => [
                    'User-Agent' => 'External webhook client',
                ],
                'verify' => false,
                'body' => $expect,
            ]
        );
        $gateway->setWebhookEndpoint($url);

        $builder = new BatchBuilder();
        foreach ($requestData as $name => $command) {
            $buildCommand = (new Builder())->method($command['method']);
            if (isset($command['parameters'])) {
                $buildCommand->setParameters($command['parameters']);
            }
            if (isset($command['fields'])) {
                $buildCommand->setFields($command['fields']);
            }

            $builder->setCommand($buildCommand, $name);

            if (isset($command['input'])) {
                $builder->takeInputFrom(...$command['input']);
            }
        }

        if ($halt) {
            $builder->setHalt(true);
        }

        $response = $gateway->call($builder->build());
        $this->assertInstanceOf(Response::class, $response);
    }


    public function additionCallProvider(): array
    {
        return [
            [
                false,
                [
                    'test' => [
                        'method' => 'user.get',
                        'parameters' => ['ID' => 1]
                    ],
                    'first_lead' => [
                        'method' => 'crm.lead.add',
                        'fields' => [
                            'TITLE' => 'Test Title'
                        ]
                    ],
                ],
                'halt=0&cmd%5Btest%5D=user.get%3FID%3D1&cmd%5Bfirst_lead%5D=crm.lead.add%3Ffields%255BTITLE%255D%3DTest%2BTitle'
            ],
            [
                true,
                [
                    'get' => [
                        'method' => 'user.list',
                        'fields' => ['PHONE' => '89001231233']
                    ],
                    'update' => [
                        'method' => 'user.update',
                        'fields' => ['NAME' => 'Alex'],
                        'input' => ['get', 'update', 'ID', 0, 'ID']
                    ],
                ],
                'halt=1&cmd%5Bget%5D=user.list%3Ffields%255BPHONE%255D%3D89001231233&cmd%5Bupdate%5D=user.update%3Ffields%255BNAME%255D%3DAlex%26ID%3D%2524result%255Bget%255D%255B0%255D%255BID%255D'
            ],
            [
                false,
                [
                    'fields' => [
                        'method' => 'crm.deal.fields',
                    ],
                    'add' => [
                        'method' => 'crm.lead.add',
                        'input' => ['fields', 'add', 'fields[TITLE]', 0, 'NAME']
                    ],
                ],
                'halt=0&cmd%5Bfields%5D=crm.deal.fields&cmd%5Badd%5D=crm.lead.add%3Ffields%255BTITLE%255D%3D%2524result%255Bfields%255D%255B0%255D%255BNAME%255D'
            ],
        ];
    }
}
