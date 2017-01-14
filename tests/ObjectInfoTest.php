<?php

namespace CloudStorageGateway\Tests;

use CloudStorageGateway\ObjectInfo;
use \DateTime;

class ObjectInfoTest extends \PHPUnit_Framework_TestCase
{
    public function testGetContentTypeReturnsContentType()
    {
        $objInfo = new ObjectInfo("content-type", 111, "tag", new DateTime("2016-01-01"));
        $this->assertEquals("content-type", $objInfo->getContentType());
    }
}
