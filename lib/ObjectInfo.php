<?php

namespace CloudStorageGateway;

use DateTime;
use JsonSerializable;

class ObjectInfo implements JsonSerializable
{
    protected string $url;

    protected string $contentType;

    protected int $contentLength;

    protected string $eTag;

    protected DateTime $lastModified;

    public function __construct(string $url, string $contentType, int $contentLength, string $eTag, DateTime $lastModified)
    {
        $this->url = $url;
        $this->contentType = $contentType;
        $this->contentLength = $contentLength;
        $this->eTag = $eTag;
        $this->lastModified = $lastModified;
    }

    public function getURL(): string
    {
        return $this->url;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getContentLength(): int
    {
        return $this->contentLength;
    }

    public function getETag(): string
    {
        return $this->eTag;
    }

    public function getLastModified(): DateTime
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
