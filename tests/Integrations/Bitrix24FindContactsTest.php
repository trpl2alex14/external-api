<?php

namespace ExternalApi\Tests\Integrations;

use ExternalApi\Bitrix24\BatchBuilder;
use ExternalApi\Bitrix24\BatchResponse;
use ExternalApi\Bitrix24\ContactBuilder;
use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Contracts\FilterInterface;
use ExternalApi\ExternalApi;
use PHPUnit\Framework\TestCase;


class Bitrix24FindContactsTest extends TestCase
{
    private array $config = [];

    private Gateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();

        if (file_exists(__DIR__ . '/config.php')) {
            $this->config = include(__DIR__ . '/config.php');
        }

        $this->gateway = ExternalApi::create('bitrix24');
        $this->gateway->setWebhookEndpoint($this->config['bitrix24_webhook_endpoint']);
    }


    public function test_bitrix24_find_contacts_by_phone_or_email()
    {
        $contact = [
            'phone' => '+7 900 123-12-12',
            'email' => 'test@test.ru',
            'first_name' => 'алексей',
            'last_name' => 'тест'
        ];

        $findEmailBuilder = (new ContactBuilder())
            ->method('findBy')
            ->setParameter('email', $contact['email']);

        $findPhoneBuilder = (new ContactBuilder())
            ->method('findBy')
            ->setParameter('phone', $contact['phone']);

        $contactListBuilder = (new ContactBuilder())
            ->select('first_name', 'last_name')
            ->where(function (FilterInterface $filter) use ($contact) {
                return $filter
                    ->contains('name', $contact['first_name'])
                    ->equal('last_name', $contact['last_name']);
            });

        $batchBuilder = (new BatchBuilder())
            ->setCommand($findEmailBuilder, 'find.email')
            ->setCommand($findPhoneBuilder, 'find.phone')
            ->setCommand($contactListBuilder, 'list.phone')
            ->takeInputFrom('find.phone', null, 'filter[ID]', 'CONTACT')
            ->setCommand($contactListBuilder, 'list.email')
            ->takeInputFrom('find.email', null, 'filter[ID]', 'CONTACT');


        $response = $this->gateway->call($batchBuilder);

        $this->assertInstanceOf(BatchResponse::class, $response);
        $this->assertArrayHasKey('list.phone', $response->getResult());
        $this->assertArrayHasKey('list.email', $response->getResult());
        $this->assertArrayHasKey('find.phone', $response->getResult());
        $this->assertArrayHasKey('find.email', $response->getResult());
    }

}
