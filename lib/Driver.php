<?php

namespace CloudStorageGateway;

interface Driver
{
    public function getObjectInfo($bucket, $key);

    public function getObject($bucket, $key);

    public function getObjectAuthenticatedURL($bucket, $name, $lifetime=300);

    public function deleteObject($bucket, $key);

    public function copyObject($fromBucket, $fromKey, $toBucket, $toKey);

    public function putObjectFile($srcFilepath, $destBucket, $destKey, $contentType=null, $isPublic=false);
}
