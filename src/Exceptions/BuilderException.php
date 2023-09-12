<?php

namespace ExternalApi\Exceptions;

use Exception;


class BuilderException extends  Exception
{
    public function __construct(string $message)
    {
        parent::__construct('Builder exception:'.$message);
    }


    public static function unknownCommand($name): BuilderException
    {
        return new self("unknown command is set '$name'");
    }


    public static function commandNotSet(): BuilderException
    {
        return new self("Command not set");
    }
}