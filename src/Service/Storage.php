<?php

namespace App\Service;

use Google\Cloud\Storage\StorageClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class Storage
{
    private $storage;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $sluggerInterface)
    {
        $config = [
            'keyFilePath' => $targetDirectory,
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
    public function uploadMealImage(UploadedFile $source)
    {
        $file = fopen($source, 'r');
        $image_info = getimagesize($source);

        $orginalName = pathinfo($source->getClientOriginalName(), PATHINFO_FILENAME);
        $objectName = '/images/'.$this->slugger->slug($orginalName).'.'.$source->guessExtension();

        $object = $this->storage->upload($file, [
            'name' => $objectName,
            'namefile' => $orginalName,
            'width' => $image_info[0],
            'height' => $image_info[1],
        ]);
        $object->acl()->add('allUsers', 'READER');
        $object->update([
        ]);

        return $object->info();
    }
}
