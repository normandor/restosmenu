<?php

namespace App\Service;

use http\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    /**
     * @param UploadedFile $file
     * @param String|null  $path
     * @param String|null  $filename
     *
     * @return string
     */
    public function upload(UploadedFile $file, String $path = null, String $filename = null): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        if (null === $filename) {
            $safeFilename = $this->slugger->slug($originalFilename);
            $filename = (string) $safeFilename;
        }

        $filename .= '.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(). $path, $filename);
        } catch (FileException $e) {
            throw $e;
            // ... handle exception if something happens during file upload
        }

        return $filename;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
