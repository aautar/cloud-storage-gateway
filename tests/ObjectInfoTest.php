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
    
    public function testGetContentLengthReturnsContentLength()
    {
        $objInfo = new ObjectInfo("content-type", 111, "tag", new DateTime("2016-01-01"));
        $this->assertEquals(111, $objInfo->getContentLength());
    }    
    
    public function testGetETagReturnsETag()
    {
        $objInfo = new ObjectInfo("content-type", 111, "tag", new DateTime("2016-01-01"));
        $this->assertEquals("tag", $objInfo->getETag());
    }
    
    public function testGetLetLastModifiedReturnsLastModifiedTimestamp()
    {
        $objInfo = new ObjectInfo("content-type", 111, "tag", new DateTime("2016-01-01"));
        $this->assertEquals(new DateTime("2016-01-01"), $objInfo->getLastModified());
    }        
}
