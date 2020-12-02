<?php

namespace CloudStorageGateway\Tests;

use CloudStorageGateway\ObjectInfo;
use \DateTime;

class ObjectInfoTest extends \PHPUnit\Framework\TestCase
{
    public function testGetUrlReturnsObjectUrl()
    {
        $objInfo = new ObjectInfo("https://url", "content-type", 111, "tag", new DateTime("2016-01-01"));
        $this->assertEquals("https://url", $objInfo->getURL());
    }

    public function testGetContentTypeReturnsContentType()
    {
        $objInfo = new ObjectInfo("https://url", "content-type", 111, "tag", new DateTime("2016-01-01"));
        $this->assertEquals("content-type", $objInfo->getContentType());
    }
    
    public function testGetContentLengthReturnsContentLength()
    {
        $objInfo = new ObjectInfo("https://url", "content-type", 111, "tag", new DateTime("2016-01-01"));
        $this->assertEquals(111, $objInfo->getContentLength());
    }    
    
    public function testGetETagReturnsETag()
    {
        $objInfo = new ObjectInfo("https://url", "content-type", 111, "tag", new DateTime("2016-01-01"));
        $this->assertEquals("tag", $objInfo->getETag());
    }
    
    public function testGetLetLastModifiedReturnsLastModifiedTimestamp()
    {
        $objInfo = new ObjectInfo("https://url", "content-type", 111, "tag", new DateTime("2016-01-01"));
        $this->assertEquals((new DateTime("2016-01-01"))->format(DateTime::ATOM), $objInfo->getLastModified()->format(DateTime::ATOM));
    }        
}
