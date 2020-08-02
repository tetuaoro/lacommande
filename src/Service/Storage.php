<?php

namespace App\Service;

use App\Entity\Provider;
use Google\Cloud\Storage\StorageClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class Storage
{
    private $storage;
    private $slugger;

    public function __construct($credentials, SluggerInterface $sluggerInterface)
    {
        $config = [
            'keyFilePath' => $credentials,
        ];

        $this->storage = (new StorageClient($config))->bucket('lacommande');
        $this->slugger = $sluggerInterface;
    }

    /**
     * Upload a file.
     *
     * @param string $objectName the name of the object
     * @param string $source     the path to the file to upload
     * @param mixed  $object
     *
     * @return array
     * @return Psr\Http\Message\StreamInterface
     */
    public function uploadMealImage(UploadedFile $source, Provider $provider)
    {
        $file = fopen($source, 'r');
        $image_info = getimagesize($source);

        $orginalName = pathinfo($source->getClientOriginalName(), PATHINFO_FILENAME);
        $objectName = '/images/'.$provider->getId().'/meal/'.$this->slugger->slug($orginalName).'-'.uniqid().'.'.$source->guessExtension();

        $object = $this->storage->upload($file, [
            'name' => $objectName,
        ]);
        $object->update([
            'metadata' => [
                'namefile' => $orginalName,
                'width' => $image_info[0],
                'height' => $image_info[1],
                'owner' => $provider->getName(),
            ],
        ], [
            'predefinedAcl' => 'publicRead',
        ]);
        $object->acl()->add('user-tetuaoropro@gmail.com', 'OWNER');

        return $object->info();
    }

    /**
     * Get file info.
     *
     * @return array
     */
    public function getInfo(string $path)
    {
        return $this->storage->object($path)->info();
    }
}
