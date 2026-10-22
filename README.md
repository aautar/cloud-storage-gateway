# cloud-storage-gateway

[![CircleCI](https://circleci.com/gh/aautar/cloud-storage-gateway.svg?style=shield)](https://circleci.com/gh/aautar/cloud-storage-gateway)

A storage-agnostic PHP interface for cloud object storage.

Application code talks to a `Gateway`, which delegates to a `Driver` for a specific storage provider. This keeps provider SDKs out of your application code and lets you swap backends (or use a fake in tests) without changing call sites.

Currently available drivers:

| Driver     | Backend | Notes                     |
|------------|---------|---------------------------|
| `S3Driver` | AWS S3  | Uses `aws/aws-sdk-php` v3 |

## Requirements

- PHP 8.1+

## Installation

The package isn't published on Packagist, so add this repository to your project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/aautar/cloud-storage-gateway"
        }
    ]
}
```

Then require it:

```sh
composer require aautar/cloud-storage-gateway:^2.0
```

## Usage

```php
use CloudStorageGateway\Gateway;
use CloudStorageGateway\S3Driver;
use CloudStorageGateway\DriverException;

$gateway = new Gateway(new S3Driver($accessKey, $secretKey, 'us-east-1'));

try {
    // Upload
    $gateway->putObjectFromString('hello world', 'my-bucket', 'greetings/hello.txt', 'text/plain');
    $gateway->putObjectFromFile('/tmp/report.pdf', 'my-bucket', 'reports/report.pdf', 'application/pdf');

    // Download
    $contents = $gateway->getObjectAsString('my-bucket', 'greetings/hello.txt'); // null if not found
    $gateway->getObjectAsFile('my-bucket', 'reports/report.pdf', '/tmp/copy.pdf'); // false if not found

    // Metadata
    $info = $gateway->getObjectInfo('my-bucket', 'reports/report.pdf'); // null if not found
    echo $info->getContentType(), ' ', $info->getContentLength();

    // Presigned URLs (default lifetime: 300 seconds)
    $downloadUrl = $gateway->getObjectAuthenticatedURL('my-bucket', 'reports/report.pdf', 600);
    $uploadUrl   = $gateway->getPutObjectPresignedURL('my-bucket', 'uploads/new.png');

    // Copy and delete
    $gateway->copyObject('my-bucket', 'reports/report.pdf', 'archive-bucket', 'reports/report.pdf');
    $gateway->deleteObject('my-bucket', 'reports/report.pdf');
} catch (DriverException $e) {
    // Provider failure; the original SDK exception is available via $e->getPrevious()
}
```

## API

All methods are available on `Gateway` (and on every `Driver`).

| Method | Returns | Notes |
|--------|---------|-------|
| `isBucketAccessible($bucket)` | `bool` | Never throws; `false` on any error |
| `getObjectInfo($bucket, $key)` | `?ObjectInfo` | `null` if the object doesn't exist |
| `getObjectAsString($bucket, $key)` | `?string` | `null` if the object doesn't exist |
| `getObjectAsFile($bucket, $key, $saveToFile)` | `bool` | `false` if the object doesn't exist |
| `getObjectAuthenticatedURL($bucket, $key, $lifetimeSec = 300)` | `string` | Time-limited URL for downloading a private object |
| `getPutObjectPresignedURL($bucket, $key, $lifetimeSec = 300)` | `string` | Time-limited URL a client can `PUT` to directly |
| `buildObjectURL($bucket, $key)` | `string` | Unsigned URL; only works for public objects |
| `putObjectFromString($data, $bucket, $key, $contentType = null, $isPublic = false)` | `bool` | Private by default |
| `putObjectFromFile($filepath, $bucket, $key, $contentType = null, $isPublic = false)` | `bool` | Private by default |
| `copyObject($fromBucket, $fromKey, $toBucket, $toKey)` | `bool` | |
| `deleteObject($bucket, $key)` | `bool` | |

### Errors

A missing object is reported through the return value (`null` / `false`), as noted above. Any other provider failure is thrown as `CloudStorageGateway\DriverException`; its code is the provider's HTTP status code and the underlying SDK exception is chained as the previous exception.

### ObjectInfo

`getObjectInfo()` returns an `ObjectInfo` describing the object:

| Method | Returns | Notes |
|--------|---------|-------|
| `getURL()` | `string` | Same value as `buildObjectURL()` |
| `getContentType()` | `string` | MIME type, e.g. `application/pdf` |
| `getContentLength()` | `int` | Size in bytes |
| `getETag()` | `string` | Surrounding quotes removed |
| `getLastModified()` | `DateTime` | |

It implements `JsonSerializable`, so `json_encode($info)` produces:

```json
{
    "url": "https://...",
    "contentType": "application/pdf",
    "contentLength": 48213,
    "eTag": "d41d8cd98f00b204e9800998ecf8427e",
    "lastModified": "2026-10-21T14:03:00+00:00"
}
```

## Writing a driver

To support another backend, implement `CloudStorageGateway\Driver` and pass it to `Gateway`:

```php
use CloudStorageGateway\Driver;

class MyStorageDriver implements Driver
{
    // implement each method of the Driver interface
}

$gateway = new Gateway(new MyStorageDriver(/* ... */));
```

Drivers should follow the same conventions as `S3Driver`: return `null` / `false` for a missing object, and wrap other provider errors in `DriverException`.

## Running tests

```sh
composer install
composer test
```

## License

MIT. See [LICENSE.md](LICENSE.md).
