<?php

namespace CloudStorageGateway\Tests;

use DateTime;
use Aws\Command;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use CloudStorageGateway\DriverException;
use CloudStorageGateway\ObjectInfo;
use GuzzleHttp\Psr7\Response;
use CloudStorageGateway\S3Driver;
use Mockery as m;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\PreserveGlobalState;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(true)]
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

    public function testBuildObjectUrlReturnsUrlFromClient()
    {
        $expectedUrl = "https://bucket.s3.us-east-2.amazonaws.com/key";

        $this->s3ClientMock
           ->shouldReceive('getObjectUrl')
           ->once()
           ->with("bucket", "key")
           ->andReturn($expectedUrl);

        $driver = $this->getDriver();
        $url = $driver->buildObjectURL("bucket", "key");

        $this->assertEquals($expectedUrl, $url);
    }

    public function testGetObjectInfoReturnsCorrectObjectInfo()
    {
        $expectedObjectInfo = new ObjectInfo("https://bucket.s3.us-east-2.amazonaws.com/key", "content-type", 111, "tag", new DateTime("2016-01-01"));

        $this->s3ClientMock
           ->shouldReceive('getObjectUrl')
           ->once()
           ->with("bucket", "key")
           ->andReturn("https://bucket.s3.us-east-2.amazonaws.com/key");

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

    /**
     * S3 percent-decodes CopySource (path semantics, so "+" is literal), hence rawurldecode()
     */
    private function copySourceDecodesTo(string $expectedSource): \Closure
    {
        return function($params) use ($expectedSource) {
            return rawurldecode($params['CopySource']) === $expectedSource;
        };
    }

    public function testCopyObjectCopiesSourceToDestination()
    {
        $this->s3ClientMock
           ->shouldReceive('copyObject')
           ->once()
           ->with(m::on(function($params) {
               return $params['Bucket'] === "toBucket"
                   && $params['Key'] === "to/key"
                   && $this->copySourceDecodesTo("fromBucket/from/key")($params);
           }))
           ->andReturn([]);

        $driver = $this->getDriver();
        $this->assertEquals(true, $driver->copyObject("fromBucket", "from/key", "toBucket", "to/key"));
    }

    public function testCopyObjectEncodesSpacesInSourceKey()
    {
        $this->s3ClientMock
           ->shouldReceive('copyObject')
           ->once()
           ->with(m::on($this->copySourceDecodesTo("fromBucket/my file.txt")))
           ->andReturn([]);

        $driver = $this->getDriver();
        $this->assertEquals(true, $driver->copyObject("fromBucket", "my file.txt", "toBucket", "to/key"));
    }

    public function testCopyObjectEncodesPlusInSourceKey()
    {
        $this->s3ClientMock
           ->shouldReceive('copyObject')
           ->once()
           ->with(m::on($this->copySourceDecodesTo("fromBucket/a+b.txt")))
           ->andReturn([]);

        $driver = $this->getDriver();
        $this->assertEquals(true, $driver->copyObject("fromBucket", "a+b.txt", "toBucket", "to/key"));
    }

    public function testCopyObjectThrowsDriverExceptionOnS3Failure()
    {
        $s3Exception = new S3Exception("access denied", new Command("copyObject"), ['response' => new Response(403)]);

        $this->s3ClientMock
           ->shouldReceive('copyObject')
           ->once()
           ->andThrow($s3Exception);

        $driver = $this->getDriver();

        try {
            $driver->copyObject("fromBucket", "from/key", "toBucket", "to/key");
            $this->fail("Expected DriverException");
        } catch(DriverException $e) {
            $this->assertEquals(403, $e->getCode());
            $this->assertSame($s3Exception, $e->getPrevious());
        }
    }

    public function testCopyObjectThrowsDriverExceptionWithZeroCodeWhenS3FailureHasNoStatusCode()
    {
        $this->s3ClientMock
           ->shouldReceive('copyObject')
           ->once()
           ->andThrow(new S3Exception("connection error", new Command("copyObject")));

        $driver = $this->getDriver();

        $this->expectException(DriverException::class);
        $this->expectExceptionCode(0);
        $driver->copyObject("fromBucket", "from/key", "toBucket", "to/key");
    }

}
