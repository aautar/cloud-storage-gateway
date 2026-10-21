<?php

namespace CloudStorageGateway;

interface Driver
{
    public function isBucketAccessible(string $bucket): bool;

    /**
     * @throws DriverException
     */
    public function getObjectInfo(string $bucket, string $key): ?ObjectInfo;

    /**
     * @throws DriverException
     */
    public function getObjectAsString(string $bucket, string $key): ?string;

    /**
     * @throws DriverException
     */
    public function getObjectAsFile(string $bucket, string $key, string $saveToFile): bool;

    /**
     * @throws DriverException
     */
    public function getObjectAuthenticatedURL(string $bucket, string $key, int $lifetimeSec=300): string;

    /**
     * @throws DriverException
     */
    public function getPutObjectPresignedURL(string $bucket, string $key, int $lifetimeSec=300): string;

    public function buildObjectURL(string $bucket, string $key): string;

    /**
     * @throws DriverException
     */
    public function deleteObject(string $bucket, string $key): bool;

    /**
     * @throws DriverException
     */
    public function copyObject(string $fromBucket, string $fromKey, string $toBucket, string $toKey): bool;

    /**
     * @throws DriverException
     */
    public function putObjectFromString(string $data, string $bucket, string $key, ?string $contentType=null, bool $isPublic=false): bool;

    /**
     * @throws DriverException
     */
    public function putObjectFromFile(string $filepath, string $bucket, string $key, ?string $contentType=null, bool $isPublic=false): bool;
}
