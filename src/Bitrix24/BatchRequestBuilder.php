<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Common\Builder;
use ExternalApi\Exceptions\BuilderException;


class BatchRequestBuilder extends Builder
{
    public const MAX_COMMANDS = 50;

    protected string $method = 'batch';

    protected array $commands = [];


    public function __construct()
    {
        parent::__construct();

        $this->setHalt(false);
    }


    public function setHalt(bool $halt): self
    {
        $this->parameters['halt'] = $halt ? 1 : 0;

        return $this;
    }


    public function setCommand(Builder $command, ?string $name = null): self
    {
        $name = $name ?: $command->getMethod();
        $params = http_build_query($command->getData());
        $this->commands[$name] = $command->getMethod() . (!$params ? '' : '?' . $params);

        return $this;
    }


    /**
     * @throws BuilderException
     */
    public function takeInputFrom(string $name, ?string $forName, string $forParameter, ...$keys): self
    {
        $forName = $forName ?: array_key_last($this->commands);
        if(!key_exists($name, $this->commands) || is_null($forName) || !key_exists($forName, $this->commands)){
            throw BuilderException::unknownCommand("in from $name to $forName");
        }

        $params = "\$result[$name]";
        foreach ($keys as $key){
            $params .= "[$key]";
        }

        $prefix = str_contains($this->commands[$forName], '?') ? '&' : '?';

        $this->commands[$forName] .= $prefix . urlencode($forParameter) . '=' . urlencode($params);

        return $this;
    }


    /**
     * @throws BuilderException
     */
    protected function getData(): array
    {
        if(empty($this->commands)){
            throw BuilderException::commandNotSet();
        }

        if(count($this->commands) > self::MAX_COMMANDS){
            throw BuilderException:: commandLimit(self::MAX_COMMANDS);
        }

        return [
            'halt' => $this->parameters['halt'] ?? 0,
            'cmd' => $this->commands
        ];
    }
}