<?php

namespace CloudStorageGateway\Tests;

use Aws\Command;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use CloudStorageGateway\S3Driver;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class S3DriverTest extends \PHPUnit_Framework_TestCase
{
    private $s3ClientMock;

    private function getDriver()
    {
        return new S3Driver("access", "secret", "us-east-2");
    }

    public function setUp()
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
           ->shouldReceive('getCommand');

        $this->s3ClientMock
           ->shouldReceive('createPresignedRequest')
           ->once()
           ->andReturn($presignedRequest);

        $driver = $this->getDriver();
        $this->assertEquals("https://presigned-url", $driver->getPutObjectPresignedURL("testBucket", "path/to/file", 500));

    }

}
