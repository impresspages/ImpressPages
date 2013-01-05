<?php
    /**
     * @package   ImpressPages
     * @copyright Copyright (C) 2012 JSC Apro Media.
     * @license   GNU/GPL, see ip_license.html
     */

namespace Modules\administrator\repository\Transform;

abstract class Image extends Base
{

    /**
     * @param $imageFile
     * @return resource
     * @throws \Modules\administrator\repository\TransformException
     */
    protected function createImageImage($imageFile){

        $this->getMemoryNeeded($imageFile);
        $mime = self::getMimeType($imageFile);

        switch ($mime) {
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                $image = imagecreatefromjpeg($imageFile);
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
                throw new \Modules\administrator\repository\TransformException("Incompatible type. Type detected: ".$mime, \Modules\administrator\repository\TransformException::UNKNOWN_MIME_TYPE);
        }

        return $image;
    }


    /**
     * Takes memory required to process supplied image file and a bit more for future PHP operations.
     * @param resource $imageFile
     * @return bool true on success
     */
    protected function getMemoryNeeded($imageFile){
        $imageInfo = getimagesize($imageFile);
        if(!isset($imageInfo['channels']) || !$imageInfo['channels']) {
            $imageInfo['channels'] = 4;
        }
        if(!isset($imageInfo['bits']) || !$imageInfo['bits']) {
            $imageInfo['bits'] = 8;
        }

        if (!isset($imageInfo[0])) {
            $imageInfo[0] = 1;
        }

        if (!isset($imageInfo[1])) {
            $imageInfo[1] = 1;
        }

        $memoryNeeded = round(($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 + Pow(2, 16)) * 1.65);
        if (function_exists('memory_get_usage') && memory_get_usage() + $memoryNeeded > (integer) ini_get('memory_limit') * pow(1024, 2)) {
            $success = ini_set('memory_limit', (integer) ini_get('memory_limit')+ 10 + ceil(((memory_get_usage() + $memoryNeeded) - (integer) ini_get('memory_limit') * pow(1024, 2)) / pow(1024, 2)) . 'M');
        } else {
            $success = true;
        }
        return $success;
    }

    /**
     * @param resource $image
     * @param string $fileName
     * @param int $quality from 0 to 100
     * @return bool
     * @throws \Modules\administrator\repository\TransformException
     */
    protected function saveJpeg($image, $fileName, $quality) {
        if(!imagejpeg($image, $fileName, $quality)){
            throw new \Modules\administrator\repository\TransformException("Can't write to file: ".$fileName , \Modules\administrator\repository\TransformException::WRITE_PERMISSION);
        }
        return true;
    }

    /**
     * @param resource $image
     * @param string $fileName
     * @param int $quality - from 0 to 9
     * @return bool
     * @throws \Modules\administrator\repository\TransformException
     */
    protected function savePng($image, $fileName, $quality) {
        //png quality is from 0 (no compression) to 9
        $tmpQuality = $quality/10;
        $tmpQuality = 9 - $tmpQuality;
        if($tmpQuality < 0) {
            $tmpQuality = 0;
        }
        if (!imagepng($image, $fileName, $tmpQuality)) {
            throw new \Modules\administrator\repository\TransformException("Can't write to file: ".$fileName , \Modules\administrator\repository\TransformException::WRITE_PERMISSION);
        }
        return true;
    }






    /**
     * Get mime type of an image file
     * @param string $imageFile
     * @return int mixed
     * @throws \Modules\administrator\repository\TransformException
     */
    protected function getMimeType($imageFile) {
        $imageInfo = getimagesize($imageFile);
        if (isset($imageInfo[2])) {
            return $imageInfo[2];
        } else {
            throw new \Modules\administrator\repository\TransformException("Incompatible type.", \Modules\administrator\repository\TransformException::UNKNOWN_MIME_TYPE);
        }

    }


    /**
     * @param resource $imageNew
     * @param string $newFile
     * @param int $quality from 0 to 100
     * @param int $mime
     * @throws \Modules\administrator\repository\TransformException
     */
    protected function saveImage ($imageNew, $newFile, $quality, $mime){
        switch ($mime) {
            case IMAGETYPE_GIF:
            case IMAGETYPE_PNG:
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
                    self::savePng($imageNew, $newFile, $quality);
                break;
            case IMAGETYPE_JPEG2000:
            case IMAGETYPE_JPEG:
            default:
                    self::saveJpeg($imageNew, $newFile, $quality);
                break;
        }
    }

}
