<?php

namespace App\Service;

use App\Entity\Meal;
use App\Entity\Provider;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\StorageObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class Storage
{
    private $storage;
    private $env;
    private $slugger;

    public function __construct($credentials, $env, SluggerInterface $sluggerInterface)
    {
        $config = [
            'keyFilePath' => $credentials,
        ];
        $this->env = $env;

        $this->storage = (new StorageClient($config))->bucket('lacommande');
        $this->slugger = $sluggerInterface;
    }

    /**
     * Get file info.
     *
     * @return array
     */
    public function getObjectInfo(string $path)
    {
        return $this->storage->object($path)->info();
    }

    /**
     * Upload meal's image.
     *
     * @return null|array
     */
    public function uploadProviderImage(UploadedFile $source, Provider $provider)
    {
        $file = fopen($source, 'r');
        $image_info = getimagesize($source);

        $orginalName = pathinfo($source->getClientOriginalName(), PATHINFO_FILENAME);
        $objectName = $this->env.'-images/'.$provider->getId().'/provider/'.$this->slugger->slug($orginalName).'-'.uniqid().'.'.$source->guessExtension();

        if ($this->checkProviderObject($provider, $orginalName)) {
            $meta = [
                'file' => $file,
                'objectName' => $objectName,
                'orginalName' => $orginalName,
                'image_info' => $image_info,
                'provider' => $provider,
                'fullname' => $orginalName.'.'.$source->guessExtension(),
            ];

            return $this->uploadObject($meta)->info();
        }

        return false;
    }

    /**
     * Upload meal's image.
     *
     * @return null|array
     */
    public function uploadMealImage(UploadedFile $source, Provider $provider, Meal $meal)
    {
        $file = fopen($source, 'r');
        $image_info = getimagesize($source);

        $orginalName = pathinfo($source->getClientOriginalName(), PATHINFO_FILENAME);
        $objectName = $this->env.'-images/'.$provider->getId().'/meals/'.$this->slugger->slug($orginalName).'-'.uniqid().'.'.$source->guessExtension();

        if ($this->checkMealObject($meal, $orginalName)) {
            $meta = [
                'file' => $file,
                'objectName' => $objectName,
                'orginalName' => $orginalName,
                'image_info' => $image_info,
                'provider' => $provider,
                'fullname' => $orginalName.'.'.$source->guessExtension(),
            ];

            return $this->uploadObject($meta)->info();
        }

        return false;
    }

    /**
     * Remove Meal's image from storage.
     */
    public function removeProviderImage(Provider $provider)
    {
        return $this->removeProviderObject($provider);
    }

    /**
     * Remove Meal's image from storage.
     */
    public function removeMealImage(Meal $meal)
    {
        return $this->removeMealObject($meal);
    }

    /**
     * Upload an object.
     *
     * @return StorageObject
     */
    private function uploadObject(array $meta)
    {
        $file = $meta['file'];
        $objectName = $meta['objectName'];
        $orginalName = $meta['orginalName'];
        $fullname = $meta['fullname'];
        $image_info = $meta['image_info'];
        $provider = $meta['provider'];

        $object = $this->storage->upload($file, [
            'name' => $objectName,
        ]);
        $object->update([
            'metadata' => [
                'filename' => $orginalName,
                'fullname' => $fullname,
                'width' => $image_info[0],
                'height' => $image_info[1],
                'owner' => $provider->getName(),
            ],
        ], [
            'predefinedAcl' => 'publicRead',
        ]);
        $object->acl()->add('user-tetuaoropro@gmail.com', 'OWNER');

        return $object;
    }

    /**
     * Check if need to crud meal object.
     * If meal's image isn't update, do anything.
     *
     * @return bool
     */
    private function checkMealObject(Meal $meal, string $orginalName)
    {
        if ($meal && $meal->getId()) {
            if ($meal->getImgInfo()['metadata']['filename'] == $orginalName) {
                return false;
            }
            $this->removeMealObject($meal);
        }

        return true;
    }

    /**
     * Check if need to crud provider object.
     * If provider's image isn't update, do anything.
     *
     * @return bool
     */
    private function checkProviderObject(Provider $provider, string $orginalName)
    {
        if ($provider && $provider->getId()) {
            if ($provider->getImgInfo()) {
                if ($provider->getImgInfo()['metadata']['filename'] == $orginalName) {
                    return false;
                }
                $this->removeProviderObject($provider);
            }
        }

        return true;
    }

    /**
     * Remove object.
     */
    private function removeProviderObject(Provider $provider)
    {
        return $this->storage->object($provider->getImgInfo()['name'])->delete();
    }

    /**
     * Remove object.
     */
    private function removeMealObject(Meal $meal)
    {
        return $this->storage->object($meal->getImgInfo()['name'])->delete();
    }
}
