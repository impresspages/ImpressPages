<?php
/**
 * @package     Library
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\Php\Picture;

require_once(BASE_DIR.LIBRARY_DIR.'php/file/functions.php');


/*
 * Error codes:
 *   1 - Can't get required memory
 *   2 - Incompatible file MIME type
 *   3 - Can't write destination file
 *   4 - Unknown file MIME type
 *
 */


class Functions{
    public static function resize ($pictureFile, $newWidth, $newHeight, $destDir, $type, $forced, $quality) {
        
    }


    /*
     *
     */
    public static function crop ($pictureFile, $destDir, $x1, $y1, $x2, $y2, $quality) {
        global $parametersMod;

        $widthDest = $x2 - $x1;
        $heightDest = $y2 - $y1;

        $pictureInfo = getimagesize($pictureFile);
        if ($pictureInfo[0] == $widthDest && $pictureInfo[1] == $heightDest) {
            $newName = \Library\Php\File\Functions::genUnocupiedName($pictureFile, $destDir);
            copy($pictureFile, $destDir.$newName);
            return;
        }



        if (!self::getMemmoryNeeded($pictureFile) ) {
            throw new \Exception("Can't get memory needed", 1);
        }

        try {
            $image = self::createPictureImage($pictureFile);
        } catch (\Exception $e) {
            throw new \Exception ($e->getMessage(), $e->getCode(), $e);
        }

        $imageNew = imagecreatetruecolor($widthDest, $heightDest);
        imagealphablending($imageNew, false);
        imagesavealpha($imageNew,true);
        $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
        imagefilledrectangle ( $imageNew, 0, 0, $widthDest, $heightDest, $color );
        imagecopyresampled($imageNew, $image, 0,  0, $x1, $y1, $widthDest, $heightDest, $widthDest, $heightDest);

        $newName = \Library\Php\File\Functions::genUnocupiedName($pictureFile, $destDir);
        $newFile = $destDir.$newName;


        $mime = self::getMimeType($pictureFile);
        switch ($mime) {
            case IMAGETYPE_GIF:
            case IMAGETYPE_PNG:
                try {
                    self::savePng($imageNew, $newFile, $quality);
                } catch (\Exceptin $e) {
                    throw new \Exception ($e->getMessage(), $e->getCode(), $e);
                }
                break;
            case IMAGETYPE_JPEG2000:
            case IMAGETYPE_JPEG:
            default:
                try {
                    self::saveJpeg($imageNew, $newFile, $quality);
                } catch (\Exceptin $e) {
                    throw new \Exception ($e->getMessage(), $e->getCode(), $e);
                }
                break;
        }
        
       
        return $newName;

    }

    public static function saveJpeg($image, $fileName, $quality) {
        if(!imagejpeg($image, $fileName, $quality)){
            throw new \Exception("Can't write to file: ".$fileName , 3);
        }
        return true;
    }

    public static function savePng($image, $fileName, $quality) {
        //png quality is from 0 (no compression) to 9
        $tmpQuality = $quality/10;
        $tmpQuality = 9 - $tmpQuality;
        if($tmpQuality < 0) {
            $tmpQuality = 0;
        }
        if (!imagepng($image, $fileName, $tmpQuality)) {
            throw new \Exception("Can't write to file: ".$fileName , 3);
        }
        return true;
    }

    public static function getMemmoryNeeded($pictureFile){
        $pictureInfo = getimagesize($pictureFile);
        if(!isset($pictureInfo['channels']) || !$pictureInfo['channels']) {
            $pictureInfo['channels'] = 4;
        }
        if(!isset($pictureInfo['bits']) || !$pictureInfo['bits']) {
            $pictureInfo['bits'] = 8;
        }
        $memoryNeeded = round(($pictureInfo[0] * $pictureInfo[1] * $pictureInfo['bits'] * $pictureInfo['channels'] / 8 + Pow(2, 16)) * 1.65);
        if (function_exists('memory_get_usage') && memory_get_usage() + $memoryNeeded > (integer) ini_get('memory_limit') * pow(1024, 2)) {
            $success = ini_set('memory_limit', (integer) ini_get('memory_limit')+ 10 + ceil(((memory_get_usage() + $memoryNeeded) - (integer) ini_get('memory_limit') * pow(1024, 2)) / pow(1024, 2)) . 'M');
        } else {
            $success = true;
        }
        return $success;
    }

     
    public static function createPictureImage($picture){

        $mime = self::getMimeType($picture);


        switch ($mime) {
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                $image = imagecreatefromjpeg($picture);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($picture);
                imageAlphaBlending($image, false);
                imageSaveAlpha($image, true);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($picture);
                imageAlphaBlending($image, false);
                imageSaveAlpha($image, true);
                break;
            default:
                throw new \Exception("Incompatible type. Type detected: ".$mime, 2);

        }

        return $image;
    }
    
    public static function getMimeType($pictureFile) {
        $pictureInfo = getimagesize($pictureFile);
        if (isset($pictureInfo[2])) {
            return $pictureInfo[2];
        } else {
            throw new \Exception("Unknown file type.", 4);
        }
        
    }

}



