<?php

namespace CloudStorageGateway;

use DateTime;
use JsonSerializable;

class ObjectInfo implements JsonSerializable
{
    protected $url;

    protected $contentType;

    protected $contentLength;

    protected $eTag;

    protected $lastModified;

    public function __construct($url, $contentType, $contentLength, $eTag, $lastModified)
    {
        $this->url = $url;
        $this->contentType = $contentType;
        $this->contentLength = $contentLength;
        $this->eTag = $eTag;
        $this->lastModified = $lastModified;
    }

    public function getURL()
    {
        return $this->url;
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

    public function jsonSerialize(): array
    {
        return [
            'url' => $this->getUrl(),
            'contentType' => $this->getContentType(),
            'contentLength' => $this->getContentLength(),
            'eTag' => $this->getETag(),
            'lastModified' => $this->getLastModified()->format(DateTime::ATOM),
        ];
    }    
}
