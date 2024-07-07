<?php

namespace CloudStorageGateway;

interface Driver
{
    /**
     *
     * @param string $bucket
     * @return boolean
     */
    public function isBucketAccessible(string $bucket): bool;

    /**
     * @param string $bucket
     * @param string $key
     * @return ObjectInfo|null
     */
    public function getObjectInfo(string $bucket, string $key): ?ObjectInfo;

    /**
     * @param string $bucket
     * @param string $key
     * @return string|null
     */
    public function getObjectAsString(string $bucket, string $key): ?string;

    /**
     * @param string $bucket
     * @param string $key
     * @param string $saveToFile
     * @return bool
     */
    public function getObjectAsFile(string $bucket, string $key, string $saveToFile): bool;

    /**
     * @param string $bucket
     * @param string $key
     * @param int $lifetimeSec lifetime in seconds
     * @return string
     */
    public function getObjectAuthenticatedURL(string $bucket, string $key, int $lifetimeSec=300): string;

    /**
     * @param string $bucket
     * @param string $key
     * @param int $lifetimeSec lifetime in seconds
     * @return string
     */
    public function getPutObjectPresignedURL(string $bucket, string $key, int $lifetimeSec=300): string;

    /**
     *
     * @param string $bucket
     * @param string $key
     * @returns string
     */
    public function buildObjectURL($bucket, $key);

    /**
     * @param string $bucket
     * @param string $key
     * @return bool
     */
    public function deleteObject(string $bucket, string $key): bool;

    /**
     * @param string $fromBucket
     * @param string $fromKey
     * @param string $toBucket
     * @param string $toKey
     * @return bool
     */
    public function copyObject(string $fromBucket, string $fromKey, string $toBucket, string $toKey): bool;

    /**
     *
     * @param string $data
     * @param string $bucket
     * @param string $key
     * @param string $contentType
     * @param bool $isPublic
     * @return bool
     */
    public function putObjectFromString(string $data, string $bucket, string $key, string $contentType=null, bool $isPublic=false): bool;

    /**
     *
     * @param string $filepath
     * @param string $bucket
     * @param string $key
     * @param string $contentType
     * @param bool $isPublic
     * @return bool
     */
    public function putObjectFromFile(string $filepath, string $bucket, string $key, string $contentType=null, bool $isPublic=false): bool;
}
