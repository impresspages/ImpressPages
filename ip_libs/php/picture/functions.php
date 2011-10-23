<?php
/**
 * @package     Library
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

namespace Library\Php\Picture;

require_once(BASE_DIR.LIBRARY_DIR.'php/file/functions.php');



class Functions{

    const ERROR_MEMORY = 1; //Can't get required memory
    const ERROR_INCOMPATIBLE = 2; //Incompatible file MIME type
    const ERROR_WRITE = 3; //Can't write destination file
    const ERROR_UNKNOWN_MIME = 4; //Can't write destination file
    const ERROR_UNKNOWN_CROP_TYPE = 5; //Unknown crop type
    
    const CROP_TYPE_FIT = 1; //resize to fit
    const CROP_TYPE_CROP = 2; //crop image if it don't fit
    const CROP_TYPE_WIDTH = 3; //resize to width
    const CROP_TYPE_HEIGHT = 4; //resize to height

    /**
     * @param string $pictureFile
     * @param int $widthDest required width
     * @param int $heightDest required height
     * @param string $destDir typicaly BASE_DIR.IMAGE_URL or BASE_DIR.TMP_IMAGE_URL
     * @param string $type
     * Available types:
     *  fit - resize to fit
     *  crop - crop image if it don't fit
     *  width - resize to width
     *  height - resize to height
     * @param bool $forced if true, resizes image even if she fits to specified size (is smaller than required)
     * @param int $quality from 0 (biggest compression) to  100 (best quality)
     * @return string file name of resized image in destDir folder
     */
    public static function resize ($pictureFile, $widthDest, $heightDest, $destDir, $type, $forced, $quality) {
        $pictureInfo = getimagesize($pictureFile);

        if (!self::resizeRequired($pictureInfo[0], $pictureInfo[1], $widthDest, $heightDest, $type, $forced)) {
            $newName = \Library\Php\File\Functions::genUnocupiedName($pictureFile, $destDir);
            copy($pictureFile, $destDir.$newName);
            return $newName;
        }



        if (!self::getMemmoryNeeded($pictureFile) ) {
            throw new \Exception("Can't get memory needed", self::ERROR_MEMORY);
        }

        try {
            $image = self::createPictureImage($pictureFile);
        } catch (\Exception $e) {
            throw new \Exception ($e->getMessage(), $e->getCode(), $e);
        }


        $imageNew = self::resizeImage($image, $widthDest, $heightDest, $pictureInfo[0], $pictureInfo[1], $type);

        $newName = \Library\Php\File\Functions::genUnocupiedName($pictureFile, $destDir);
        $newFile = $destDir.$newName;


        $mime = self::getMimeType($pictureFile);
        try {
            self::saveImage($imageNew, $newFile, $quality, $mime);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }


         
        return $newName;


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
            return $newName;
        }



        if (!self::getMemmoryNeeded($pictureFile) ) {
            throw new \Exception("Can't get memory needed", self::ERROR_MEMORY);
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
        try {
            self::saveImage($imageNew, $newFile, $quality, $mime);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }

         
        return $newName;

    }

    public static function saveJpeg($image, $fileName, $quality) {
        if(!imagejpeg($image, $fileName, $quality)){
            throw new \Exception("Can't write to file: ".$fileName , self::ERROR_WRITE);
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
            throw new \Exception("Can't write to file: ".$fileName , self::ERROR_WRITE);
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
                throw new \Exception("Incompatible type. Type detected: ".$mime, self::ERROR_INCOMPATIBLE);

        }

        return $image;
    }

    public static function getMimeType($pictureFile) {
        $pictureInfo = getimagesize($pictureFile);
        if (isset($pictureInfo[2])) {
            return $pictureInfo[2];
        } else {
            throw new \Exception("Unknown file type.", self::ERROR_UNKNOWN_MIME);
        }

    }



    /**
     * @access private
     */
    private static function resizeImage($image, $widthDest, $heightDest, $widthSource, $heightSource, $type){

        $dest_proportion = $widthDest / $heightDest;
        $sourceProportion = (double)$widthSource / (double)$heightSource;

        
         
         
        switch($type){
            case self::CROP_TYPE_FIT:
                if($sourceProportion > $dest_proportion){
                    $width_skirtumas = 0;
                    $height_skirtumas = ($heightDest - $widthDest/($sourceProportion))/2;
                }else{
                    $width_skirtumas = ($widthDest - $heightDest*($sourceProportion))/2;
                    $height_skirtumas = 0;
                }

                if($height_skirtumas == 0 && $width_skirtumas != 0)
                $widthDest = $heightDest * $sourceProportion;
                elseif($height_skirtumas != 0 && $width_skirtumas == 0){
                    $heightDest = $widthDest / $sourceProportion;
                }

                $imageNew = imagecreatetruecolor($widthDest, $heightDest);
                imagealphablending($imageNew, false);
                imagesavealpha($imageNew,true);
                $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
                imagefilledrectangle ( $imageNew, 0, 0, $widthDest, $heightDest, $color );
                imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthDest, $heightDest, $widthSource, $heightSource);
                break;
            case self::CROP_TYPE_CROP:
                if($sourceProportion > $dest_proportion){
                    $width_skirtumas = ($widthSource - $heightSource*($dest_proportion))/2;
                    $height_skirtumas = 0;
                }else{
                    $width_skirtumas = 0;
                    $height_skirtumas = ($heightSource - $widthSource/$dest_proportion)/2;
                }

                $imageNew = imagecreatetruecolor($widthDest, $heightDest);
                imagealphablending($imageNew, false);
                imagesavealpha($imageNew,true);
                $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
                imagefilledrectangle ( $imageNew, 0, 0, $widthDest, $heightDest, $color );
                imagecopyresampled($imageNew, $image, 0, 0, $width_skirtumas, $height_skirtumas, $widthDest, $heightDest, $widthSource-$width_skirtumas*2, $heightSource-$height_skirtumas*2);
                break;
            case self::CROP_TYPE_WIDTH:

                $heightTmp = $widthDest/$sourceProportion;

                $imageNew = imagecreatetruecolor($widthDest, $heightTmp);
                imagealphablending($imageNew, false);
                imagesavealpha($imageNew,true);
                $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
                imagefilledrectangle ( $imageNew, 0, 0, $widthDest, $heightTmp, $color );
                imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthDest, $heightTmp, $widthSource, $heightSource);

                if($heightTmp > $heightDest){
                    $image = $imageNew;
                    $imageNew = imagecreatetruecolor($widthDest, $heightDest);
                    $color = imagecolorallocate ($imageNew, 255, 255, 255 );
                    imagefilledrectangle ( $imageNew, 0, 0, $widthDest, $heightDest, $color );
                    imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthDest, $heightDest, $widthDest, $heightDest);
                }
                break;
            case self::CROP_TYPE_HEIGHT:
                $widthTmp = $heightDest*$sourceProportion;

                $imageNew = imagecreatetruecolor($widthTmp, $heightDest);
                imagealphablending($imageNew, false);
                imagesavealpha($imageNew,true);
                $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
                imagefilledrectangle ( $imageNew, 0, 0, $widthTmp, $heightDest, $color );
                imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthTmp, $heightDest, $widthSource, $heightSource);

                if($widthTmp > $widthDest){
                    $image = $imageNew;
                    $imageNew = imagecreatetruecolor($widthDest, $heightDest);
                    $color = imagecolorallocate ($imageNew, 255, 255, 255 );
                    imagefilledrectangle ( $imageNew, 0, 0, $widthDest, $heightDest, $color );
                    imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthDest, $heightDest, $widthDest, $heightDest);
                }
                break;
            default:
                throw new \Exception("Unknown crop type: ".$type, self::ERROR_UNKNOWN_CROP_TYPE);

        }
         

        return $imageNew;

    }

    private static function resizeRequired($widthS, $heightS, $widthT, $heightT, $type, $forced) {
        switch($type){
            case self::CROP_TYPE_FIT:
                if ($forced) {
                    return $widthS != $widthT || $heightS != $heightT;
                } else {
                    return $widthS > $widthT || $heightS > $heightT;
                }
                break;
            case self::CROP_TYPE_CROP:
                if ($forced) {
                    return $widthS != $widthT || $heightS != $heightT;
                } else {
                    return $widthS > $widthT || $heightS > $heightT;
                }
                break;
            case self::CROP_TYPE_WIDTH:
                if ($forced) {
                    return $widthS != $widthT;
                } else {
                    return $widthS > $widthT;
                }
                break;
            case self::CROP_TYPE_HEIGHT:
                if ($forced) {
                    return $heightS != $heightT;
                } else {
                    return $heightS > $heightT;
                }
                break;
            default:
                throw new \Exception("Unknown crop type: ".$type, self::ERROR_UNKNOWN_CROP_TYPE);
        }
    }


    private static function saveImage ($imageNew, $newFile, $quality, $mime){
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
    }

}



