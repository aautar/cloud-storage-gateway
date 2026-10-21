<?php

namespace CloudStorageGateway;

class Gateway
{
    protected Driver $driver;

    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    public function isBucketAccessible(string $bucket): bool
    {
        return $this->driver->isBucketAccessible($bucket);
    }

    /**
     * @throws DriverException
     */
    public function getObjectInfo(string $bucket, string $key): ?ObjectInfo
    {
        return $this->driver->getObjectInfo($bucket, $key);
    }

    /**
     * @throws DriverException
     */
    public function getObjectAsString(string $bucket, string $key): ?string
    {
        return $this->driver->getObjectAsString($bucket, $key);
    }

    /**
     * @throws DriverException
     */
    public function getObjectAsFile(string $bucket, string $key, string $saveToFile): bool
    {
        return $this->driver->getObjectAsFile($bucket, $key, $saveToFile);
    }

    /**
     * @throws DriverException
     */
    public function getObjectAuthenticatedURL(string $bucket, string $key, int $lifetimeSec=300): string
    {
        return $this->driver->getObjectAuthenticatedURL($bucket, $key, $lifetimeSec);
    }

    /**
     * @throws DriverException
     */
    public function getPutObjectPresignedURL(string $bucket, string $key, int $lifetimeSec=300): string
    {
        return $this->driver->getPutObjectPresignedURL($bucket, $key, $lifetimeSec);
    }

    public function buildObjectURL(string $bucket, string $key): string
    {
        return $this->driver->buildObjectURL($bucket, $key);
    }

    /**
     * @throws DriverException
     */
    public function deleteObject(string $bucket, string $key): bool
    {
        return $this->driver->deleteObject($bucket, $key);
    }

    /**
     * @throws DriverException
     */
    public function copyObject(string $fromBucket, string $fromKey, string $toBucket, string $toKey): bool
    {
        return $this->driver->copyObject($fromBucket, $fromKey, $toBucket, $toKey);
    }

    /**
     * @throws DriverException
     */
    public function putObjectFromString(string $data, string $bucket, string $key, ?string $contentType=null, bool $isPublic=false): bool
    {
        return $this->driver->putObjectFromString($data, $bucket, $key, $contentType, $isPublic);
    }

    /**
     * @throws DriverException
     */
    public function putObjectFromFile(string $filepath, string $bucket, string $key, ?string $contentType=null, bool $isPublic=false): bool
    {
        return $this->driver->putObjectFromFile($filepath, $bucket, $key, $contentType, $isPublic);
    }
}
