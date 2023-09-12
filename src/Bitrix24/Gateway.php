<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Common\Gateway as BaseGateway;
use ExternalApi\Common\Response as BaseResponse;
use ExternalApi\Contracts\ApiRequestInterface;


class Gateway extends BaseGateway
{
    protected string $bodyEncodeMethod = 'http_build_query';

    public const MAX_CALL_PER_SECOND = 2;

    protected bool $throttle = true;


    public function getName(): string
    {
        return 'bitrix24';
    }


    public function setThrottle(bool $throttle): self
    {
        $this->throttle = $throttle;

        return $this;
    }


    public function call(ApiRequestInterface $request): Response
    {
        $time_start = microtime(true);

        $response = parent::call($request);
        $response = get_class($response) === BaseResponse::class
            ? new Response($response->getRawResponse())
            : $response;

        $time_end = microtime(true);

        $sleep = (1 / self::MAX_CALL_PER_SECOND - ($time_end - $time_start));
        if ($sleep > 0 && $this->throttle) {
            usleep($sleep * 1000000);
        }

        return $response;
    }
}