<?php

namespace ExternalApi\Tests\Integrations;

use ExternalApi\Bitrix24\BatchBuilder;
use ExternalApi\Bitrix24\Responses\BatchResponse;
use ExternalApi\Bitrix24\ContactBuilder;
use ExternalApi\Bitrix24\Responses\ContactFoundResponse;
use ExternalApi\Bitrix24\Gateway;
use ExternalApi\Contracts\FilterInterface;
use ExternalApi\Exceptions\BuilderException;
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
        $expectIDs = [
            'find.email' => [19604, 19640],
            'find.phone' => [19604],
            'list.email' => [19604],
            'list.phone' => [19604],
        ];

        $contact = [
            'phone' => '+7 900 123-12-12',
            'email' => 'testapi@test.ru',
            'first_name' => 'алексей',
            'last_name' => 'тестапи'
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
                    ->contains('first_name', $contact['first_name'])
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

        $ids = $response->getResult()['find.email']['CONTACT'];
        $this->assertEquals($expectIDs['find.email'], $ids);
        $ids = $response->getResult()['find.phone']['CONTACT'];
        $this->assertEquals($expectIDs['find.phone'], $ids);

        $ids = array_map(fn($item) => $item['ID'], $response->getResult()['list.phone']);
        $this->assertEquals($expectIDs['list.phone'], $ids);
        $ids = array_map(fn($item) => $item['ID'], $response->getResult()['list.email']);
        $this->assertEquals($expectIDs['list.email'], $ids);
    }


    public function test_bitrix24_find_contacts_by_contact()
    {
        $contact = $this->gateway
            ->createEntity('contact', [
                'id' => 19604,
                'phone' => ['+7 900 123-12-12', '+7 900 123-12-10'],
                'email' => 'testapi@test.ru',
                'first_name' => 'алексей',
                'last_name' => 'тестапи'
            ]);

        $response = $this->gateway
            ->createRequestBuilder('contact')
            ->method('findBy')
            ->byContact($contact)
            ->call();

        $this->assertInstanceOf(ContactFoundResponse::class, $response);
        $this->assertCount(1, $response->getResource()->getItems());

        $item = $response->getResource()->getItems()[0];
        $this->assertEquals($contact->id, $item->id);
        $this->assertEquals([19604, 19640], $response->getLikeContactIds());
    }


    public function test_bitrix24_can_not_find_contacts_without_phone_and_email()
    {
        $this->expectException(BuilderException::class);

        $contact = $this->gateway
            ->createEntity('contact', [
                'first_name' => 'алексей',
                'last_name' => 'тест'
            ]);

        $response = $this->gateway
            ->createRequestBuilder('contact')
            ->method('findBy')
            ->byContact($contact)
            ->call();
    }
}
