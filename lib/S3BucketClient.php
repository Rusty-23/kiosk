<?php
require_once './vendor/autoload.php';

use Aws\S3\S3Client;


class S3BucketClient
{
    
    private $ACCESS_KEY_P1 = 'AKIAWZMWWW';
    private $ACCESS_KEY_P2 = 'EWNLHDJVA4';
    private $SECRET_KEY_P1 = 'q76qXrWZK3mc7GkMRjKN';
    private $SECRET_KEY_P2 = 'vg29AOWC7muviJv8JlHA';

    private $REGION;
    private $BUCKET;

    private $client;

    public function __construct($BUCKET, $REGION = 'ap-southeast-1')
    {
        $this->BUCKET = $BUCKET;
        $this->REGION = $REGION;

        $this->client = new S3Client([
            'credentials' => [
                'key' => $this->ACCESS_KEY_P1 . $this->ACCESS_KEY_P2,
                'secret' => $this->SECRET_KEY_P1 . $this->SECRET_KEY_P2,
            ],
            'region' => $this->REGION,
        ]);
    }

    public function getPresignedUrl($key, $expiration = '+6 days', $contentType = null)
    {
        try {
            $command = $this->client->getCommand('GetObject', [
                'Bucket' => $this->BUCKET,
                'Key' => $key,
            ]);

            $headers = ['ContentDisposition' => 'inline; filename="' . basename($key) . '"'];
            if (!empty($contentType)) {
                $headers['ContentType'] = $contentType;
            }

            $request = $this->client->createPresignedRequest(
                $command,
                $expiration,
                $headers,
            );

            return (string) $request->getUri();
        } catch (Exception $e) {
            // Handle exceptions appropriately, e.g., log the error or throw a custom exception
            throw new RuntimeException('Failed to generate presigned URL: ' . $e->getMessage());
        }
    }

}
