<?php

namespace CloudStorageGateway;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class S3Driver implements Driver
{
    const HTTP_NOT_FOUND = 404;

    protected S3Client $s3Client;

    /**
     * @throws DriverException
     */
    protected function createPresignedRequest(string $command, string $bucket, string $key, int $lifetimeSec=300): string
    {
        $cmd = $this->s3Client->getCommand($command, [
            'Bucket' => $bucket,
            'Key'    => $key
        ]);

        try {
            $req = $this->s3Client->createPresignedRequest($cmd, "+{$lifetimeSec} seconds");
            return (string)$req->getUri();
        } catch(S3Exception $e) {
            throw new DriverException("S3 client failure", $e->getStatusCode(), $e);
        }
    }

    public function __construct(string $accessKey, string $secretKey, string $region)
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

    public function isBucketAccessible(string $bucket): bool
    {
        try
        {
            $this->s3Client->headBucket([
                'Bucket' => $bucket
            ]);
        }
        catch (S3Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @throws DriverException
     */
    public function getObjectInfo(string $bucket, string $key): ?ObjectInfo
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
            $this->buildObjectURL($bucket, $key),
            $result['ContentType'],
            $result['ContentLength'],
            trim($result['ETag'], '"'),
            new \DateTime($result['LastModified'])
        );
    }

    /**
     * @throws DriverException
     */
    public function getObjectAsString(string $bucket, string $key): ?string
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
     * @throws DriverException
     */
    public function getObjectAsFile(string $bucket, string $key, string $saveToFile): bool
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

    /**
     * @throws DriverException
     */
    public function getObjectAuthenticatedURL(string $bucket, string $key, int $lifetimeSec=300): string
    {
        return $this->createPresignedRequest('GetObject', $bucket, $key, $lifetimeSec);
    }

    /**
     * @throws DriverException
     */
    public function getPutObjectPresignedURL(string $bucket, string $key, int $lifetimeSec=300): string
    {
        return $this->createPresignedRequest('PutObject', $bucket, $key, $lifetimeSec);
    }

    public function buildObjectURL(string $bucket, string $key): string
    {
        return $this->s3Client->getObjectUrl($bucket, $key);
    }

    /**
     * @throws DriverException
     */
    public function deleteObject(string $bucket, string $key): bool
    {
        try
        {
            $this->s3Client->deleteObject([
                    'Bucket' => $bucket,
                    'Key' => $key
                ]);
        } catch(S3Exception $e) {
            throw new DriverException("S3 client failure", $e->getStatusCode(), $e);
        }

        return true;
    }

    /**
     * @throws DriverException
     */
    public function copyObject(string $fromBucket, string $fromKey, string $toBucket, string $toKey): bool
    {
        try
        {
            $this->s3Client->copyObject([
                'CopySource' => $fromBucket . "/" . rawurlencode($fromKey),
                'Bucket' => $toBucket,
                'Key' => $toKey
            ]);
        } catch(S3Exception $e) {
            throw new DriverException("S3 client failure", $e->getStatusCode(), $e);
        }

        return true;
    }

    /**
     * @throws DriverException
     */
    public function putObjectFromString(string $data, string $bucket, string $key, ?string $contentType=null, bool $isPublic=false): bool
    {
        $reqParams = [
            'Body' => $data,
            'Bucket' => $bucket,
            'Key' => $key,
            'ACL' => 'private'
        ];

        if($contentType) {
            $reqParams['ContentType'] = $contentType;
        }

        if($isPublic) {
            $reqParams['ACL'] = 'public-read';
        }

        try {
            $this->s3Client->putObject($reqParams);
        } catch(S3Exception $e) {
            throw new DriverException("S3 client failure", $e->getStatusCode(), $e);
        }

        return true;
    }

    /**
     * @throws DriverException
     */
    public function putObjectFromFile(string $filepath, string $bucket, string $key, ?string $contentType=null, bool $isPublic=false): bool
    {
        $reqParams = [
            'Bucket' => $bucket,
            'Key' => $key,
            'SourceFile' => $filepath,
            'ACL' => 'private'
        ];

        if($contentType) {
            $reqParams['ContentType'] = $contentType;
        }

        if($isPublic) {
            $reqParams['ACL'] = 'public-read';
        }

        try {
            $this->s3Client->putObject($reqParams);
        } catch(S3Exception $e) {
            throw new DriverException("S3 client failure", $e->getStatusCode(), $e);
        }

        return true;
    }
}
