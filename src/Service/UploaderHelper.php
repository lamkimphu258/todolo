<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploaderHelper
{
    public function __construct(
        private SluggerInterface $slugger,
        private string $avatarsPath
    ) {
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string
     */
    public function uploadUserImage(UploadedFile $uploadedFile): string
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        try {
            $uploadedFile->move(
                $this->avatarsPath,
                $newFilename
            );
        } catch (FileException $e) {
            dd($e->getTraceAsString());
        }

        return $newFilename;
    }
}
