<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace Ip\Internal\Repository\Transform;

abstract class Image extends \Ip\Internal\Repository\Transform
{

    /**
     * Create image resource from image file and alocate required memory
     * @param $imageFile
     * @return resource
     * @throws \Ip\Exception\Repository\Transform
     */
    protected function createImageImage($imageFile)
    {

        $this->getMemoryNeeded($imageFile);

        $mime = $this->getMimeType($imageFile);


        switch ($mime) {
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                $originalSetting = ini_set('gd.jpeg_ignore_warning', 1);
                $image = imagecreatefromjpeg($imageFile);
                if ($originalSetting !== false) {
                    ini_set('gd.jpeg_ignore_warning', $originalSetting);
                }
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($imageFile);
                imageAlphaBlending($image, false);
                imageSaveAlpha($image, true);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($imageFile);
                imageAlphaBlending($image, false);
                imageSaveAlpha($image, true);
                break;
            default:
                throw new \Ip\Exception\Repository\Transform("Incompatible type. Type detected: " . esc(
                    $mime
                ), array('mime' => $mime));
        }

        return $image;
    }

    protected function createEmptyImage($width, $height)
    {
        $trueColor = 1;

        \Ip\Internal\System\Helper\SystemInfo::allocateMemory($width * $height * (2.2 + ($trueColor * 3)));
        return imagecreatetruecolor($width, $height);
    }

    /**
     * Takes memory required to process supplied image file and a bit more for future PHP operations.
     * @param resource $imageFile
     * @return bool true on success
     */
    protected function getMemoryNeeded($imageFile)
    {
        if (!file_exists($imageFile)) {
            return 0;
        }
        $imageInfo = getimagesize($imageFile);
        if (!isset($imageInfo['channels']) || !$imageInfo['channels']) {
            $imageInfo['channels'] = 4;
        }
        if (!isset($imageInfo['bits']) || !$imageInfo['bits']) {
            $imageInfo['bits'] = 8;
        }

        if (!isset($imageInfo[0])) {
            $imageInfo[0] = 1;
        }

        if (!isset($imageInfo[1])) {
            $imageInfo[1] = 1;
        }

        $memoryNeeded = round(
            ($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 + Pow(2, 16)) * 1.65
        );
        $success = \Ip\Internal\System\Helper\SystemInfo::allocateMemory($memoryNeeded);

        return $success;
    }

    /**
     * @param resource $image
     * @param string $fileName
     * @param int $quality from 0 to 100
     * @return bool
     * @throws \Ip\Exception\Repository\Transform
     */
    protected function saveJpeg($image, $fileName, $quality)
    {
        if (ipConfig()->get('interlaceJpeg', true)) {
            imageinterlace( $image, TRUE);
        }
        if (!imagejpeg($image, $fileName, (int)$quality)) {
            throw new \Ip\Exception\Repository\Transform("Can't write to file: " . esc(
                $fileName
            ), array('filename' => $fileName));
        }
        return true;
    }

    /**
     * @param resource $image
     * @param string $fileName
     * @param int $quality - from 0 to 9
     * @return bool
     * @throws \Ip\Exception\Repository\Transform
     */
    protected function savePng($image, $fileName, $compression)
    {
        if (ipConfig()->get('interlacePng', false )) {
            imageinterlace( $image, TRUE);
        }
        if (!imagepng($image, $fileName, $compression)) {
            throw new \Ip\Exception\Repository\Transform("Can't write to file: " . esc(
                $fileName
            ), array('filename' => $fileName));
        }
        return true;
    }


    /**
     * Get mime type of an image file
     * @param string $imageFile
     * @return int mixed
     * @throws \Ip\Exception\Repository\Transform
     */
    protected function getMimeType($imageFile)
    {
        $imageInfo = getimagesize($imageFile);
        if (isset($imageInfo[2])) {
            return $imageInfo[2];
        } else {
            throw new \Ip\Exception\Repository\Transform("Incompatible type.", array('filename' => $imageFile));
        }

    }


    /**
     * @param resource $imageNew
     * @param string $newFile
     * @param int $quality from 0 to 100
     * @throws \Ip\Exception\Repository\Transform
     */
    protected function saveImage($imageNew, $newFile, $quality)
    {

        $pathInfo = pathinfo($newFile);

        switch (strtolower(isset($pathInfo['extension']) ? $pathInfo['extension'] : '')) {
            case 'png':
                //fill transparent places with white.
                /*$width = imagesx($imageNew);
                $height = imagesy($imageNew);
                $imageBg = imagecreatetruecolor($width, $height);
                imagealphablending($imageBg, false);
                imagesavealpha($imageBg,true);
                imagealphablending($imageNew, true);
                imagesavealpha($imageNew,true);
                $color = imagecolorallocatealpha($imageBg, 255, 255, 0, 0);
                imagefilledrectangle ( $imageBg, 0, 0, $width, $height, $color );
                imagecopymerge($imageBg, $imageNew, 0, 0, 0, 0, $width, $height, 50);
                */
                self::savePng($imageNew, $newFile, 9); //9 - maximum compression. PNG is always lossless
                break;
            case 'jpg':
            case 'jpeg':
            default:
                self::saveJpeg($imageNew, $newFile, $quality);
                break;
        }
    }

    protected function fixSourceRatio($x1, $y1, $x2, $y2, $widthDest, $heightDest)
    {
        $widthSource = $x2 - $x1;
        $heightSource = $y2 - $y1;
        if ($heightSource > 0 && $widthSource > 0) {
            //fix ratio if needed
            if ($heightSource == 0) {
                $sourceRatio = 1; //to avoid division by zero
            } else {
                $sourceRatio = $widthSource / $heightSource;
            }

            if ($heightDest == 0) {
                $destRatio = 1; //to avoid division by zero
            } else {
                $destRatio = $widthDest / $heightDest;
            }
            if ($sourceRatio > $destRatio) {
                //lower source width
                $requiredWidth = $heightSource * $destRatio;
                $diff = $widthSource - $requiredWidth;
                $x1 = $x1 + round($diff / 2);
                $x2 = $x2 - round($diff / 2);
            } elseif ($sourceRatio < $destRatio) {
                //lower source height
                $requiredHeight = $widthSource / $destRatio;
                $diff = $heightSource - $requiredHeight;
                $y1 = $y1 + round($diff / 2);
                $y2 = $y2 - round($diff / 2);
            }
        }
        return array($x1, $y1, $x2, $y2);
    }

}
