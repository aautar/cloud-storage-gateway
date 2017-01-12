<?php

namespace CloudStorageGateway;

class Gateway
{
    protected $driver;

    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    public function getObjectInfo($bucket, $key)
    {
        return $this->driver->getObjectInfo($bucket, $key);
    }
}
