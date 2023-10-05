# External gateway for API 

ExternalAPI - is a unified gateway library for working with various API providers.

**Why use ExternalAPI instead of a gateway's official PHP package/example code?**

* Because you can learn one API and use it in multiple projects
* Because if you need to change external api service you won't need to rewrite your code
* Because most official API SDK libraries are a mess
* Because most API gateways have exceptionally poor documentation


## Bitrix24 API REST Usage

This package implements [Bitirx24 REST API](https://dev.1c-bitrix.ru/rest_help/index.php) support for ExternalAPI.

Example:

```php
use ExternalApi\ExternalApi;

$gateway = ExternalApi::create('bitrix24');
$gateway
    ->setWebhookEndpoint('https://**HOME**.bitrix24.ru/rest/*USERID*/***CODE***')
    ->setEntitySettings([
        'city' => 'UF_1212'    
    ], 'deal');    

$entity = $gateway
    ->createEntity('deal')
    ->setFields([
        'title' => 'Deal - 1',
        'city' => 'Ufa'
    ]);

$requestBuilder = $gateway
    ->createRequestBuilder('deal')
    ->method('create')
    ->setFields($entity->getFields());
```

How call API?

* Method 1
```php
$response = $requestBuilder->call();

$entity = $response->getResource();

$id = $entity->id;
```

* Method 2
```php

$response = $gateway->call($requestBuilder->build());

$entity = $response->getResource();
```