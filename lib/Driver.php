<?php

namespace CloudStorageGateway;

interface Driver
{
    /**
     *
     * @param string $bucket
     * @return boolean
     */
    public function isBucketAccessible($bucket);

    /**
     * @param string $bucket
     * @param string $key
     * @return ObjectInfo|null
     */
    public function getObjectInfo($bucket, $key);

    /**
     * @param string $bucket
     * @param string $key
     * @return string
     */
    public function getObjectAsString($bucket, $key);

    /**
     * @param string $bucket
     * @param string $key
     * @param string $saveToFile
     * @return bool
     */
    public function getObjectAsFile($bucket, $key, $saveToFile);

    /**
     * @param string $bucket
     * @param string $key
     * @param int lifetime in seconds
     * @return string
     */
    public function getObjectAuthenticatedURL($bucket, $key, $lifetimeSec=300);

    /**
     * @param string $bucket
     * @param string $key
     * @param int lifetime in seconds
     * @return string
     */
    public function getPutObjectPresignedURL($bucket, $key, $lifetimeSec=300);

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
    public function deleteObject($bucket, $key);

    /**
     * @param string $fromBucket
     * @param string $fromKey
     * @param string $toBucket
     * @param string $toKey
     * @return bool
     */
    public function copyObject($fromBucket, $fromKey, $toBucket, $toKey);

    /**
     *
     * @param string $data
     * @param string $bucket
     * @param string $key
     * @param string $contentType
     * @param bool $isPublic
     * @return bool
     */
    public function putObjectFromString($data, $bucket, $key, $contentType=null, $isPublic=false);

    /**
     *
     * @param string $filepath
     * @param string $bucket
     * @param string $key
     * @param string $contentType
     * @param bool $isPublic
     * @return bool
     */
    public function putObjectFromFile($filepath, $bucket, $key, $contentType=null, $isPublic=false);
}
