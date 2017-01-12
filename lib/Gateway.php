<?php

namespace CloudStorageGateway;

class Gateway
{
    /**
     * @var Driver
     */
    protected $driver;

    /**
     * @param \CloudStorageGateway\Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @param string $bucket
     * @param string $key
     * @return ObjectInfo|null
     */
    public function getObjectInfo($bucket, $key)
    {
        return $this->driver->getObjectInfo($bucket, $key);
    }

    /**
     * @param string $bucket
     * @param string $key
     * @return string
     */
    public function getObjectAsString($bucket, $key)
    {
        return $this->driver->getObjectAsString($bucket, $key);
    }

    /**
     * @param string $bucket
     * @param string $key
     * @param string $saveToFile
     * @return bool
     */
    public function getObjectAsFile($bucket, $key, $saveToFile)
    {
        return $this->driver->getObjectAsFile($bucket, $key, $saveToFile);
    }

    /**
     * @param string $bucket
     * @param string $key
     * @param int lifetime in seconds
     * @return string
     */
    public function getObjectAuthenticatedURL($bucket, $key, $lifetimeSec=300)
    {
        return $this->driver->getObjectAuthenticatedURL($bucket, $key, $lifetimeSec);
    }

    /**
     * @param string $bucket
     * @param string $key
     * @return bool
     */
    public function deleteObject($bucket, $key)
    {
        return $this->driver->deleteObject($bucket, $key);
    }

    /**
     * @param string $fromBucket
     * @param string $fromKey
     * @param string $toBucket
     * @param string $toKey
     * @return bool
     */
    public function copyObject($fromBucket, $fromKey, $toBucket, $toKey)
    {
        return $this->driver->copyObject($fromBucket, $fromKey, $toBucket, $toKey);
    }

    /**
     *
     * @param string $data
     * @param string $bucket
     * @param string $key
     * @param string $contentType
     * @param bool $isPublic
     * @return bool
     */
    public function putObjectFromString($data, $bucket, $key, $contentType=null, $isPublic=false)
    {
        return $this->driver->putObjectFromString($data, $bucket, $key, $contentType, $isPublic);
    }

    /**
     *
     * @param string $filepath
     * @param string $bucket
     * @param string $key
     * @param string $contentType
     * @param bool $isPublic
     * @return bool
     */
    public function putObjectFromFile($filepath, $bucket, $key, $contentType=null, $isPublic=false)
    {
        return $this->driver->putObjectFromFile($filepath, $bucket, $key, $contentType, $isPublic);
    }
}
