<?php

namespace App\Service;

use Aws\S3\S3Client;

class AwsS3
{
    private $s3;

    public function __construct($version, $region, $credentials)
    {
        $config = [
            'version' => $version,
            'region' => $region,
            'credentials' => $credentials,
        ];
        $this->s3 = new S3Client($config);
    }

    public function getClient()
    {
        return $this->s3;
    }
}
