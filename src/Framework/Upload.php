<?php

namespace Framework;

use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;

class Upload
{
    protected $path;

    protected $formats = [];

    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }
    
    /**
     * Permet d'uploader un fichier
     *
     * @param  UploadedFileInterface $file
     * @param  string $oldFile
     * @param  string $fileName
     * @return string
     */
    public function upload(UploadedFileInterface $file, ?string $oldFile = null, ?string $fileName = null): ?string
    {
        if ($file->getError() === UPLOAD_ERR_OK) {
            $this->delete($oldFile);
            $fileName = $fileName ?: $file->getClientFilename();
            $targetPath = $this->addCopySuffix($this->path . '/' . $fileName);
            $dirname = pathinfo($targetPath, PATHINFO_DIRNAME);
            if (!file_exists($dirname)) {
                mkdir($dirname, 777, true);
            }
            $file->moveTo($targetPath);
            $this->generateFormats($targetPath);
            return pathinfo($targetPath)['basename'];
        }
        return null;
    }



    public function delete(?string $oldFile = null): void
    {
        if ($oldFile) {
            $oldFile = $this->path . DIRECTORY_SEPARATOR . $oldFile;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            foreach ($this->formats as $format => $value) {
                $oldFileWithFormat = $this->getPathWithSuffix($oldFile, $format);
                if (file_exists($oldFileWithFormat)) {
                    unlink($oldFileWithFormat);
                }
            }
        }
    }

    private function addCopySuffix(string $targetPath): string
    {
        if (file_exists($targetPath)) {
            return $this->addCopySuffix($this->getPathWithSuffix($targetPath, 'copy'));
        }
        return $targetPath;
    }

    private function getPathWithSuffix(string $path, string $suffix): string
    {
        $info = pathinfo($path);
        return $info['dirname'] .
            '/' .
            $info['filename'] .
            '_' . $suffix . '.' .
            $info['extension'];
    }

    private function generateFormats($targetPath)
    {
        foreach ($this->formats as $format => $size) {
            $manager = new ImageManager(['driver' => 'gd']);
            $destination = $this->getPathWithSuffix($targetPath, $format);
            [$width, $height] = $size;
            $manager->make($targetPath)->fit($width, $height)->save($destination);
        }
    }
}
