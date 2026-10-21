<?php

namespace CloudStorageGateway;

use Exception;
use Throwable;

class DriverException extends Exception
{
    public function __construct(string $message = "", ?int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code ?? 0, $previous);
    }
}
