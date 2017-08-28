<?php

namespace App\Model;

use Imagine\Exception\RuntimeException;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Uploader
 */
class Uploader
{
    /**
     * @var string
     */
    protected $targetDir;

    /**
     * Uploader constructor.
     * @param $uploaderParams
     */
    public function __construct($uploaderParams)
    {
        $this->targetDir = '/' . trim($uploaderParams['upload_dir'], '/');
    }

    /**
     * @param UploadedFile $file
     * @param bool $fileIsTmp
     *
     * @return array
     */
    public function upload(UploadedFile $file, $fileIsTmp = false)
    {
        $data = $this->move($file, $fileIsTmp);

        return $data;
    }

    /**
     * @param $imageData
     *
     * @return string
     */
    public function getWebUrlOfImage($imageData)
    {
        return sprintf('images/%s/%s.%s', $imageData['path'], $imageData['name'], $imageData['extension']);
    }

    /**
     * @param UploadedFile $file
     * @param bool $fileIsTmp
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function move(UploadedFile $file, $fileIsTmp = false)
    {
        if (!$file->isValid()) {
            throw new \Exception('File not valid');
        }

        $this->ensureExtensionIsAllowed($file);

        //move to config
        $neededWidth = 320;
        $neededHeight = 240;

        $fileName = md5(microtime());

        $target = $this->generatePath($fileName, $fileIsTmp);

        $res = array(
            'mimeType' => $file->getMimeType(),
            'extension' => $file->guessExtension(),
            'name' => $fileName,
            'path' => $target[1],
            'originName' => $file->getClientOriginalName(),
            'size' => $file->getSize()
        );

        $imageSize = getimagesize($file->getRealPath());

        $width = $imageSize[0];
        $height = $imageSize[1];

        $fullFileName = $target[0] . '/' . $fileName . '.' . $file->guessExtension();

        if ($neededWidth > $width && $neededHeight > $height) {
            if (!$file->move($fullFileName)) {
                throw new \Exception(sprintf('Error moving file', $target[1]));
            }
        } else {
            $this->resizeBeforeMove($file, $fullFileName, $neededWidth, $neededHeight);
        }

        return $res;
    }

    /**
     * @param UploadedFile $file
     * @param $fullFileName
     * @param $newWidth
     * @param $newHeight
     *
     * @throws RuntimeException
     */
    protected function resizeBeforeMove(UploadedFile $file, $fullFileName, $newWidth, $newHeight)
    {
        $tmpFile = $file->getRealPath();
        $imagine = new Imagine();

        $imageSize = getimagesize($tmpFile);

        if ($imageSize[0] >= $imageSize[1]) {
            $koe = $imageSize[0] / $newWidth;
            $newHeight = ceil($imageSize[1] / $koe);
        } else {
            $koe = $imageSize[1] / $newHeight;
            $newWidth = ceil($imageSize[0] / $koe);
        }

        $sizeBox = new Box($newWidth, $newHeight);

        $imagine->open($tmpFile)
            ->resize($sizeBox)
            ->save($fullFileName);
    }

    /**
     * @param UploadedFile $file
     *
     * @throws \Exception
     */
    protected function ensureExtensionIsAllowed(UploadedFile $file)
    {
        if (!in_array($file->guessExtension(), ['jpg', 'jpeg', 'png'])) {
            throw new \Exception(sprintf('Extension "%s" is not allowed', $file->guessExtension()));
        }
    }

    /**
     * @param $fileName
     * @param bool $fileIsTmp
     *
     * @return array
     */
    protected function generatePath($fileName, $fileIsTmp)
    {
        $dir = substr($fileName, 0, 2) . '/' . substr($fileName, 2, 2);

        if ($fileIsTmp) {
            $dir .= '/tmp';
        }

        $this->createDir($this->targetDir . '/' . $dir);

        return [$this->targetDir . '/' . $dir, $dir];
    }

    /**
     * @param $path
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function createDir($path)
    {
        if (!is_dir($path)) {
            if (false === @mkdir($path, 0777, true)) {
                throw new \Exception(sprintf('Unable to create the "%s" directory', $path));
            }
        }
    }
}