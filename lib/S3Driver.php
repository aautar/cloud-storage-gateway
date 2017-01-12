<?php

namespace CloudStorageGateway;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class S3Driver
{
    const HTTP_NOT_FOUND = 404;

    /**
     * @var S3Client
     */
    protected $s3Client;

    /**
     *
     * @param string $accessKey
     * @param string $secretKey
     * @param string $region
     */
    public function __construct($accessKey, $secretKey, $region)
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => $region,
            'credentials' => [
                'key' => $accessKey,
                'secret' => $secretKey,
            ]
        ]);
    }

    /**
     * @param string $bucket
     * @param string $key
     * @return ObjectInfo|null
     */
    public function getObjectInfo($bucket, $key)
    {
        $result = [];

        try
        {
            $result = $this->s3Client->headObject([
                'Bucket' => $bucket,
                'Key' => $key
            ]);
        }
        catch (S3Exception $e) {
            if($e->getStatusCode() === self::HTTP_NOT_FOUND) {
                return null;
            }

            throw new DriverException("S3 client failure", $e->getStatusCode(), $e);
        }

        return new ObjectInfo(
                $result['ContentType'],
                $result['ContentLength'],
                trim($result['ETag'], '"'),
                new \DateTime($result['LastModified'])
            );
    }

    /**
     * @param string $bucket
     * @param string $key
     * @return string
     */
    public function getObjectAsString($bucket, $key)
    {
        $result = null;

        $reqParams = [
            'Bucket' => $bucket,
            'Key' => $key
        ];

        try {
            $response = $this->s3Client->getObject($reqParams);
            $result = (string)$response['Body'];
        } catch(S3Exception $e) {
            
            if($e->getStatusCode() === self::HTTP_NOT_FOUND) {
                return null;
            }

            throw new DriverException("S3 client failure", $e->getStatusCode(), $e);
        }

        return $result;
    }


    /**
     * @param string $bucket
     * @param string $key
     * @param string $saveToFile
     * @return bool
     */
    public function getObjectAsFile($bucket, $key, $saveToFile)
    {
        $reqParams = [
            'Bucket' => $bucket,
            'Key' => $key,
            'SaveAs' => $saveToFile
        ];

        try {
            $this->s3Client->getObject($reqParams);
        } catch(S3Exception $e) {

            if($e->getStatusCode() === self::HTTP_NOT_FOUND) {
                return false;
            }

            throw new DriverException("S3 client failure", $e->getStatusCode(), $e);
        }

        return true;
    }

    public function getObjectAuthenticatedURL($bucket, $name, $lifetime=300)
    {
        $this->s3Client->createPresignedRequest($args);
    }

    public function deleteObject($bucket, $key)
    {
        $this->s3Client->deleteObject();
    }

    public function copyObject($fromBucket, $fromKey, $toBucket, $toKey)
    {
        $this->s3Client->copy($fromB, $fromK, $destB, $destK);
    }

    public function putObjectFile($srcFilepath, $destBucket, $destKey, $contentType=null, $isPublic=false)
    {
        $this->s3Client->putObject($args);
    }
}
