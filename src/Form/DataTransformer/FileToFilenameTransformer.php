<?php

namespace App\Form\DataTransformer;

use App\Entity\Issue;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\File;

class FileToFilenameTransformer implements DataTransformerInterface
{
    private $targetDirectory;
    private $entityManager;

    public function __construct($targetDirectory, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $filename
     *
     * @return File|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function transform($filename)
    {
        if (!$filename) {
            return;
        }

        $file = new File($this->targetDirectory.$filename);

        if (null === $file) {
            $privateErrorMessage = sprintf('File "%s" does not exist!', $filename);
            $publicErrorMessage = 'The given "{{ value }}" value is not a valid issue number.';

            $failure = new TransformationFailedException($privateErrorMessage);
            $failure->setInvalidMessage($publicErrorMessage, [
                '{{ value }}' => $filename,
            ]);

            throw $failure;
        }

        return $file;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  File|null $file
     *
     * @return string
     */
    public function reverseTransform($file)
    {
        if (null === $file) {
            return '';
        }

        return $file->getFilename();
    }
}
