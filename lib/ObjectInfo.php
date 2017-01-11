<?php

namespace CloudStorageGateway;

class ObjectInfo
{
    protected $contentType;

    protected $contentLength;

    protected $eTag;

    protected $lastModified;

    public function __construct($contentType, $contentLength, $eTag, $lastModified)
    {
        $this->contentType = $contentType;
        $this->contentLength = $contentLength;
        $this->eTag = $eTag;
        $this->lastModified = $lastModified;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function getContentLength()
    {
        return $this->contentLength;
    }

    public function getETag()
    {
        return $this->eTag;
    }

    public function getLastModified()
    {
        return $this->lastModified;
    }
}
