<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal;


class ImageHelper
{

    const ERROR_MEMORY = 1; // Can't get required memory.
    const ERROR_INCOMPATIBLE = 2; // Incompatible file MIME type.
    const ERROR_WRITE = 3; // Can't write destination file.
    const ERROR_UNKNOWN_MIME = 4; // Can't write destination file.
    const ERROR_UNKNOWN_CROP_TYPE = 5; // Unknown crop type.

    const CROP_TYPE_FIT = 1; // Resize to fit
    const CROP_TYPE_CROP = 2; // Crop image if it don't fit
    const CROP_TYPE_WIDTH = 3; // Resize to width
    const CROP_TYPE_HEIGHT = 4; // Resize to height

    /**
     * @param string $imageFile
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
     * @param int $quality from 0 (biggest compression) to 100 (best quality)
     * @return string file name of resized image in destDir folder
     * @throws \Exception
     * @throws \Ip\Exception
     */
    public static function resize($imageFile, $widthDest, $heightDest, $destDir, $type, $forced, $quality)
    {
        $imageInfo = getimagesize($imageFile);

        if (!self::resizeRequired($imageInfo[0], $imageInfo[1], $widthDest, $heightDest, $type, $forced)) {
            $newName = \Ip\Internal\File\Functions::genUnoccupiedName($imageFile, $destDir);
            copy($imageFile, $destDir . $newName);
            return $newName;
        }

        if (!self::getMemoryNeeded($imageFile)) {
            throw new \Exception("Can't get memory needed", self::ERROR_MEMORY);
        }

        try {
            $image = self::createImageImage($imageFile);
        } catch (\Exception $e) {
            throw new \Ip\Exception($e->getMessage(), $e->getCode(), $e);
        }

        $imageNew = self::resizeImage($image, $widthDest, $heightDest, $imageInfo[0], $imageInfo[1], $type);

        $newName = \Ip\Internal\File\Functions::genUnoccupiedName($imageFile, $destDir);
        $newFile = $destDir . $newName;

        $mime = self::getMimeType($imageFile);
        try {
            self::saveImage($imageNew, $newFile, $quality, $mime);
        } catch (\Exception $e) {
            throw new \Ip\Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $newName;
    }

    /**
     * @param string $imageFile
     * @param string $destDir
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param int $quality
     * @param int $widthDest
     * @param int $heightDest
     * @return string
     * @throws \Ip\Exception
     * @throws \Exception
     */
    public static function crop($imageFile, $destDir, $x1, $y1, $x2, $y2, $quality, $widthDest, $heightDest)
    {
        if ($widthDest === null) {
            $widthDest = $x2 - $x1;
        }
        if ($heightDest === null) {
            $heightDest = $y2 - $y1;
        }

        $imageInfo = getimagesize($imageFile);

        if ($imageInfo[0] == $widthDest && $imageInfo[1] == $heightDest && $x1 == 0 && $y1 == 0) { // Don't need to crop or resize.
            $newName = \Ip\Internal\File\Functions::genUnoccupiedName($imageFile, $destDir);
            copy($imageFile, $destDir . $newName);
            return $newName;
        }

        if (!self::getMemoryNeeded($imageFile)) {
            throw new \Ip\Exception("Can't get memory needed", self::ERROR_MEMORY);
        }

        try {
            $image = self::createImageImage($imageFile);
        } catch (\Exception $e) {
            throw new \Ip\Exception($e->getMessage(), $e->getCode(), $e);
        }

        if ($x2 - $x1 > imagesx($image) || $y2 - $y1 > imagesy(
                $image
            ) || $x1 < 0 || $y1 < 0
        ) { // Cropping area goes out of image edge. Fill transparent.
            /**
             * Negative coordinates x1, y1 are possible.
             * This part of code just adds tarnsparent edges in this way making $image required proportions.
             * We don't care about the size in this step.
             */
            $tmpImage = imagecreatetruecolor($x2 - $x1, $y2 - $y1);
            imagealphablending($tmpImage, false);
            imagesavealpha($tmpImage, true);
            $color = imagecolorallocatealpha($tmpImage, 255, 255, 255, 127);
            imagefilledrectangle($tmpImage, 0, 0, $x2 - $x1, $y2 - $y1, $color);
            if ($x1 >= 0) {
                $sx1 = $x1;
                $dx1 = 0;
            } else {
                $sx1 = 0;
                $dx1 = -$x1;
            }
            if ($y1 >= 0) {
                $sy1 = $y1;
                $dy1 = 0;
            } else {
                $sy1 = 0;
                $dy1 = -$y1;
            }
            if ($x2 - $x1 > imagesx($image)) {
                $sx2 = imagesx($image);
                // $dx2 = $x2 - $x1;
                $dx2 = $dx1 + imagesx($image);
            } else {
                $sx2 = $x2;
                $dx2 = imagesx($tmpImage);
            }
            if ($y2 - $y1 > imagesy($image)) {
                $sy2 = imagesy($image);
                $dy2 = $dy1 + imagesy($image);
            } else {
                $sy2 = $y2;
                $dy2 = imagesy($tmpImage);
            }

            imagecopyresampled(
                $tmpImage,
                $image,
                $dx1,
                $dy1,
                $sx1,
                $sy1,
                $dx2 - $dx1,
                $dy2 - $dy1,
                $sx2 - $sx1,
                $sy2 - $sy1
            );
            $image = $tmpImage;

            $sx1 = 0;
            $sy1 = 0;
            $sx2 = imagesx($image);
            $sy2 = imagesy($image);

            /* Transparency required. Transform to png. */
            $mime = IMAGETYPE_PNG;

            $path_parts = pathinfo($imageFile);
            if ($path_parts['extension'] != 'png') {
                $tmpImageName = $path_parts['filename'] . '.png';
            } else {
                $tmpImageName = $imageFile;
            }
            $newName = \Ip\Internal\File\Functions::genUnoccupiedName($tmpImageName, $destDir);
        } else {
            $sx1 = $x1;
            $sx2 = $x2;
            $sy1 = $y1;
            $sy2 = $y2;
            $mime = self::getMimeType($imageFile);
            $newName = \Ip\Internal\File\Functions::genUnoccupiedName($imageFile, $destDir);
        }

        /**
         * Our $image is required proportions. The only thing we need to do is to scale the image and save.
         */

        $imageNew = imagecreatetruecolor($widthDest, $heightDest);
        imagealphablending($imageNew, false);
        imagesavealpha($imageNew, true);
        $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
        imagefilledrectangle($imageNew, 0, 0, $widthDest, $heightDest, $color);
        imagecopyresampled($imageNew, $image, 0, 0, $sx1, $sy1, $widthDest, $heightDest, $sx2 - $sx1, $sy2 - $sy1);

        $newFile = $destDir . $newName;

        try {
            self::saveImage($imageNew, $newFile, $quality, $mime);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $newName;
    }

    /**
     * @param resource $image
     * @param string $fileName
     * @param int $quality
     * @return bool
     * @throws \Exception
     */
    public static function saveJpeg($image, $fileName, $quality)
    {
        if (!imagejpeg($image, $fileName, $quality)) {
            throw new \Exception("Can't write to file: " . $fileName, self::ERROR_WRITE);
        }
        return true;
    }

    /**
     * @param resource $image
     * @param string $fileName
     * @param int $quality
     * @return bool
     * @throws \Exception
     */
    public static function savePng($image, $fileName, $quality)
    {
        // Png quality is from 0 (no compression) to 9.
        $tmpQuality = $quality / 10;
        $tmpQuality = 9 - $tmpQuality;
        if ($tmpQuality < 0) {
            $tmpQuality = 0;
        }
        if (!imagepng($image, $fileName, $tmpQuality)) {
            throw new \Exception("Can't write to file: " . $fileName, self::ERROR_WRITE);
        }
        return true;
    }

    /**
     * @param $imageFile
     * @return bool|null
     */
    public static function getMemoryNeeded($imageFile)
    {
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

        $a64kb = 65536;
        $bytesNeeded = round(
            ($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 + $a64kb) * 1.65
        );

        return \Ip\Internal\System\Helper\SystemInfo::allocateMemory($bytesNeeded);
    }

    /**
     * @param string $image
     * @return resource
     * @throws \Exception
     */
    public static function createImageImage($image)
    {
        $mime = self::getMimeType($image);

        switch ($mime) {
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                $originalSetting = ini_set('gd.jpeg_ignore_warning', 1);
                $image = imagecreatefromjpeg($image);
                if ($originalSetting !== false) {
                    ini_set('gd.jpeg_ignore_warning', $originalSetting);
                }
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($image);
                imageAlphaBlending($image, false);
                imageSaveAlpha($image, true);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($image);
                imageAlphaBlending($image, false);
                imageSaveAlpha($image, true);
                break;
            default:
                throw new \Exception("Incompatible type. Type detected: " . $mime, self::ERROR_INCOMPATIBLE);
        }

        return $image;
    }

    /**
     * @param string $imageFile
     * @return string
     * @throws \Exception
     */
    public static function getMimeType($imageFile)
    {
        $imageInfo = getimagesize($imageFile);
        if (isset($imageInfo[2])) {
            return $imageInfo[2];
        } else {
            throw new \Exception("Unknown file type.", self::ERROR_UNKNOWN_MIME);
        }
    }


    /**
     * @param $image
     * @param $widthDest
     * @param $heightDest
     * @param $widthSource
     * @param $heightSource
     * @param $type
     * @return resource
     * @throws \Exception
     */
    private static function resizeImage($image, $widthDest, $heightDest, $widthSource, $heightSource, $type)
    {
        $dest_proportion = $widthDest / $heightDest;
        $sourceProportion = (double)$widthSource / (double)$heightSource;

        switch ($type) {
            case self::CROP_TYPE_FIT:
                if ($sourceProportion > $dest_proportion) {
                    $width_skirtumas = 0;
                    $height_skirtumas = ($heightDest - $widthDest / ($sourceProportion)) / 2;
                } else {
                    $width_skirtumas = ($widthDest - $heightDest * ($sourceProportion)) / 2;
                    $height_skirtumas = 0;
                }

                if ($height_skirtumas == 0 && $width_skirtumas != 0) {
                    $widthDest = $heightDest * $sourceProportion;
                }
                elseif ($height_skirtumas != 0 && $width_skirtumas == 0) {
                    $heightDest = $widthDest / $sourceProportion;
                }

                $imageNew = imagecreatetruecolor($widthDest, $heightDest);
                imagealphablending($imageNew, false);
                imagesavealpha($imageNew, true);
                $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
                imagefilledrectangle($imageNew, 0, 0, $widthDest, $heightDest, $color);
                imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthDest, $heightDest, $widthSource, $heightSource);
                break;
            case self::CROP_TYPE_CROP:
                if ($sourceProportion > $dest_proportion) {
                    $width_skirtumas = ($widthSource - $heightSource * ($dest_proportion)) / 2;
                    $height_skirtumas = 0;
                } else {
                    $width_skirtumas = 0;
                    $height_skirtumas = ($heightSource - $widthSource / $dest_proportion) / 2;
                }

                $imageNew = imagecreatetruecolor($widthDest, $heightDest);
                imagealphablending($imageNew, false);
                imagesavealpha($imageNew, true);
                $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
                imagefilledrectangle($imageNew, 0, 0, $widthDest, $heightDest, $color);
                imagecopyresampled(
                    $imageNew,
                    $image,
                    0,
                    0,
                    $width_skirtumas,
                    $height_skirtumas,
                    $widthDest,
                    $heightDest,
                    $widthSource - $width_skirtumas * 2,
                    $heightSource - $height_skirtumas * 2
                );
                break;
            case self::CROP_TYPE_WIDTH:
                $heightTmp = $widthDest / $sourceProportion;

                $imageNew = imagecreatetruecolor($widthDest, $heightTmp);
                imagealphablending($imageNew, false);
                imagesavealpha($imageNew, true);
                $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
                imagefilledrectangle($imageNew, 0, 0, $widthDest, $heightTmp, $color);
                imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthDest, $heightTmp, $widthSource, $heightSource);

                if ($heightTmp > $heightDest) {
                    $image = $imageNew;
                    $imageNew = imagecreatetruecolor($widthDest, $heightDest);
                    $color = imagecolorallocate($imageNew, 255, 255, 255);
                    imagefilledrectangle($imageNew, 0, 0, $widthDest, $heightDest, $color);
                    imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthDest, $heightDest, $widthDest, $heightDest);
                }
                break;
            case self::CROP_TYPE_HEIGHT:
                $widthTmp = $heightDest * $sourceProportion;

                $imageNew = imagecreatetruecolor($widthTmp, $heightDest);
                imagealphablending($imageNew, false);
                imagesavealpha($imageNew, true);
                $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
                imagefilledrectangle($imageNew, 0, 0, $widthTmp, $heightDest, $color);
                imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthTmp, $heightDest, $widthSource, $heightSource);

                if ($widthTmp > $widthDest) {
                    $image = $imageNew;
                    $imageNew = imagecreatetruecolor($widthDest, $heightDest);
                    $color = imagecolorallocate($imageNew, 255, 255, 255);
                    imagefilledrectangle($imageNew, 0, 0, $widthDest, $heightDest, $color);
                    imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $widthDest, $heightDest, $widthDest, $heightDest);
                }
                break;
            default:
                throw new \Exception("Unknown crop type: " . $type, self::ERROR_UNKNOWN_CROP_TYPE);
        }

        return $imageNew;
    }


    /**
     * @param $widthS
     * @param $heightS
     * @param $widthT
     * @param $heightT
     * @param $type
     * @param $forced
     * @return bool
     * @throws \Exception
     */
    private static function resizeRequired($widthS, $heightS, $widthT, $heightT, $type, $forced)
    {
        switch ($type) {
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
                throw new \Exception("Unknown crop type: " . $type, self::ERROR_UNKNOWN_CROP_TYPE);
        }
    }

    /**
     * @param string $imageNew
     * @param string $newFile
     * @param int $quality
     * @param string $mime
     * @throws \Exception
     */
    private static function saveImage($imageNew, $newFile, $quality, $mime)
    {
        switch ($mime) {
            case IMAGETYPE_GIF:
            case IMAGETYPE_PNG:
                try {
                    // Fill transparent places with white.
                    /*
                    $width = imagesx($imageNew);
                    $height = imagesy($imageNew);
                    $imageBg = imagecreatetruecolor($width, $height);
                    imagealphablending($imageBg, false);
                    imagesavealpha($imageBg,true);
                    imagealphablending($imageNew, true);
                    imagesavealpha($imageNew, true);
                    $color = imagecolorallocatealpha($imageBg, 255, 255, 0, 0);
                    imagefilledrectangle($imageBg, 0, 0, $width, $height, $color);
                    imagecopymerge($imageBg, $imageNew, 0, 0, 0, 0, $width, $height, 50);
                    */
                    self::savePng($imageNew, $newFile, 9); // 9 - Maximum compression. PNG is always lossless.
                } catch (\Exception $e) {
                    throw new \Exception ($e->getMessage(), $e->getCode(), $e);
                }
                break;
            case IMAGETYPE_JPEG2000:
            case IMAGETYPE_JPEG:
            default:
                try {
                    self::saveJpeg($imageNew, $newFile, $quality);
                } catch (\Exception $e) {
                    throw new \Exception ($e->getMessage(), $e->getCode(), $e);
                }
                break;
        }
    }

}
