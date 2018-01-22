<?php

namespace CloudStorageGateway\Tests;

use CloudStorageGateway\S3Driver;

class S3DriverTest extends \PHPUnit_Framework_TestCase
{
    private function getDriver()
    {
        return new S3Driver("access", "secret", "us-east-2");
    }

    public function testBuildObjectUrlReturnsCorrectUrl()
    {
        $expectedUrl = "https://us-east-2.amazonaws.com/bucket/key";

        $driver = $this->getDriver();
        $url = $driver->buildObjectURL("bucket", "key");

        $this->assertEquals($expectedUrl, $url);
    }
}
