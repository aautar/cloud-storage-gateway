<?php

namespace CloudStorageGateway\Tests;

use DateTime;
use Aws\Command;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use CloudStorageGateway\ObjectInfo;
use CloudStorageGateway\S3Driver;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class S3DriverTest extends \PHPUnit\Framework\TestCase
{
    private $s3ClientMock;

    private function getDriver()
    {
        return new S3Driver("access", "secret", "us-east-2");
    }

    public function setUp(): void
    {
        $this->s3ClientMock = m::mock('overload:' . S3Client::class);
    }

    public function testBuildObjectUrlReturnsCorrectUrl()
    {
        $expectedUrl = "https://us-east-2.amazonaws.com/bucket/key";

        $driver = $this->getDriver();
        $url = $driver->buildObjectURL("bucket", "key");

        $this->assertEquals($expectedUrl, $url);
    }

    public function testGetObjectInfoReturnsCorrectObjectInfo()
    {
        $expectedObjectInfo = new ObjectInfo("https://us-east-2.amazonaws.com/bucket/key", "content-type", 111, "tag", new DateTime("2016-01-01"));

        $this->s3ClientMock
           ->shouldReceive('headObject')
           ->once()
           ->with(['Bucket' => "bucket", "Key" => "key"])
           ->andReturn([
               "ContentType" => "content-type",
               "ContentLength" => 111,
               "ETag" => "tag",
               "LastModified" => "2016-01-01"
           ]);

        $driver = $this->getDriver();
        $info = $driver->getObjectInfo("bucket", "key");

        $this->assertEquals($expectedObjectInfo->jsonSerialize(), $info->jsonSerialize());
    }

    public function testIsBucketAccessibleReturnsTrueForAccessibleBucket()
    {        
        $this->s3ClientMock
           ->shouldReceive('headBucket')
           ->once()
           ->with(['Bucket' => "testBucket"])
           ->andReturn([]);

        $driver = $this->getDriver();
        $this->assertEquals(true, $driver->isBucketAccessible("testBucket"));
    }

    public function testIsBucketAccessibleReturnsFalseForInAccessibleBucket()
    {
        $this->s3ClientMock
           ->shouldReceive('headBucket')
           ->once()
           ->with(['Bucket' => "testBucket"])
           ->andThrow(new S3Exception("access denied", new Command("headBucket")));

        $driver = new S3Driver("access", "secret", "us-east-2");
        $this->assertEquals(false, $driver->isBucketAccessible("testBucket"));
    }

    public function testGetPutObjectPresignedURLReturnsPresignedURL()
    {
        $presignedRequest = new class {
            public function getUri() {
                return "https://presigned-url";
            }
        };

        $this->s3ClientMock
           ->shouldReceive('getCommand')
           ->once()
           ->with("PutObject", ['Bucket' => "testBucket", "Key" => "path/to/file"]);

        $this->s3ClientMock
           ->shouldReceive('createPresignedRequest')
           ->once()
           ->andReturn($presignedRequest);

        $driver = $this->getDriver();
        $this->assertEquals("https://presigned-url", $driver->getPutObjectPresignedURL("testBucket", "path/to/file", 500));

    }

}
