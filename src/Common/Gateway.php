<?php

namespace ExternalApi\Common;

use Exception;
use ExternalApi\Contracts\ApiRequestInterface;
use ExternalApi\Contracts\GatewayInterface;
use ExternalApi\Contracts\RequestBuilderInterface;
use ExternalApi\Contracts\ResponseInterface;
use ExternalApi\Exceptions\CouldNotCallApi;
use ExternalApi\Exceptions\GatewayException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;


abstract class Gateway implements GatewayInterface
{
    private ?array $headers = [];

    private ?array $queryValues = [];

    private string $method = 'GET';

    private static array $methods = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'];

    private bool|string $verify = false;

    private string $endpoint;

    protected string $bodyEncodeMethod = 'json_encode';

    protected ?array $entitySettings = null;

    public function __construct(protected ?Client $client = null)
    {
        $this->client = $this->client ?: new Client();

        $this->setHeader('User-Agent', 'External webhook client');
    }


    public function setEntitySettings(array $entitySettings, ?string $entity = null): self
    {
        if($entity){
            $this->entitySettings = $this->entitySettings ?: [];
            $this->entitySettings[$entity] = $entitySettings;
        }else{
            $this->entitySettings = $entitySettings;
        }

        return $this;
    }


    public function setWebhookEndpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @throws GatewayException
     */
    public function getWebhookEndpoint(): string
    {
        if (empty($this->endpoint)) {
            throw GatewayException::notSetEndPoint();
        }

        return $this->endpoint;
    }


    public function setVerify(bool|string $verify = true): self
    {
        $this->verify = $verify;

        return $this;
    }


    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }


    public function setHeader(string $key, $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }


    public function setQueryValue(string $key, $value): self
    {
        $this->queryValues[$key] = $value;

        return $this;
    }

    /**
     * @throws GatewayException
     */
    public function setMethod(string $method): self
    {
        if (in_array($method, self::$methods)) {
            $this->method = $method;
        } else {
            throw GatewayException::unknownRequestMethod($method);
        }

        return $this;
    }


    public function createRequestBuilder(string $entity): RequestBuilderInterface
    {
        $class = Helper::getRequestClassName($entity, static::class);

        if (!class_exists($class)) {
            throw new RuntimeException("Class request '$class' not found");
        }

        return (new $class)->setGateway($this);
    }


    public function createEntity(string $entity, ...$args): Entity
    {
        $class = Helper::getEntityClassName($entity, static::class);

        if (!class_exists($class)) {
            throw new RuntimeException("Class entity '$class' not found");
        }

        $entityName = Helper::getEntityName($entity);
        if(isset($this->entitySettings[$entityName])){
            $args = [
                ...$args,
                $this->entitySettings[$entityName]
            ];
        }

        return new $class(...$args);
    }

    /**
     * @throws GatewayException|CouldNotCallApi
     */
    public function call(ApiRequestInterface|RequestBuilderInterface $request): ResponseInterface
    {
        if ($request instanceof RequestBuilderInterface) {
            $request = $request->build();
        }

        $url = $this->getWebhookEndpoint();

        if ($command = $request->getMethod()) {
            $url = str_ends_with($url, '/') ? $url : $url . '/';
            $url = $url . $command;
        }

        $options = $this->makeRequestOptions($request);

        if (isset($options['body'])) {
            $this->method = $this->method === 'GET' ? 'POST' : $this->method;
        }

        try {
            $response = $this->client->request($this->method, $url, $options);
        } catch (Exception | GuzzleException $e) {
            throw CouldNotCallApi::serviceRespondedException($e);
        }

        if ($response->getStatusCode() >= 300 || $response->getStatusCode() < 200) {
            throw CouldNotCallApi::serviceRespondedWithAnError($response);
        }

        return $this->makeResponse($request, $response);
    }


    protected function makeRequestOptions(ApiRequestInterface $request): array
    {
        $query = array_merge($this->queryValues, $request->getQueryValues() ?: []);

        $options = [
            'headers' => array_merge($this->headers, $request->getHeaders() ?: []),
            'verify' => $this->verify,
        ];

        if (!empty($query)) {
            $options['query'] = $query;
        }

        $body = $request->getData();
        if (!empty($body)) {
            $options['body'] = function_exists($this->bodyEncodeMethod)
                ? call_user_func($this->bodyEncodeMethod, ($body))
                : json_encode($body);
        }

        return $options;
    }


    private function makeResponse(ApiRequestInterface $request, mixed $response): Response
    {
        if (method_exists($request, 'getResponseClassName')) {
            $class = $request->getResponseClassName();

            $class = class_exists($class) ? $class : null;
        }

        $class = $class ?? Response::class;

        return new $class($response, $this);
    }
}