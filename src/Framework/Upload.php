<?php

namespace Framework;

use Psr\Http\Message\UploadedFileInterface;

class Upload
{
    protected $path;

    private $format;

    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }

    public function upload(UploadedFileInterface $file, ?string $oldFile = null): string
    {
        $this->delete($oldFile);
        $fileName = $file->getClientFilename();
        $targetPath = $this->addSuffix($this->path . '/' . $fileName);
        $dirname = pathinfo($targetPath, PATHINFO_DIRNAME);
        if (!file_exists($dirname)) {
            mkdir($dirname, 777, true);
        }
         $file->moveTo($targetPath);
        return pathinfo($targetPath)['basename'];
    }

    private function addSuffix(string $targetPath): string
    {
        if (file_exists($targetPath)) {
            $info = pathinfo($targetPath);
            $targetPath = $info['dirname'].
                    '/' .
                    $info['filename'] .
                    '_copy.' .
                    $info['extension'];
            return $this->addSuffix($targetPath);
        }
        return $targetPath;
    }

    private function delete(?string $oldFile = null): void
    {
        if ($oldFile) {
            $oldFile = $this->path . DIRECTORY_SEPARATOR . $oldFile;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
    }
}
