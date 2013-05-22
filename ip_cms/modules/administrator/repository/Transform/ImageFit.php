<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 JSC Apro Media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\repository\Transform;

class ImageFit extends Image
{
    protected $width;
    protected $height;
    protected $quality;
    protected $forced;

    /**
     * @param int $width width of area where image should fit
     * @param int $height height of area where image should fit
     * @param int $quality image quoality from 0 to 100
     * @param bool $forced if true, and supplied image has different proportions, resulting image will have white edges to make image exactly $Width x $height
     */
    public function __construct($width, $height, $quality = null, $forced = false)
    {
        global $parametersMod;
        if ($quality === null)
        {
            $quality = $parametersMod->getValue('standard', 'configuration', 'advanced_options', 'default_image_quality');
        }

        $this->width = $width;
        $this->height = $height;
        $this->quality = $quality;
        $this->forced = $forced;
    }

    public function transform($sourceFile, $destinationFile)
    {
        //check if modifications are needed
        if (!$this->resizeRequired($sourceFile)) {
            copy($sourceFile, $destinationFile);
            return;
        }

        //modify image
        $image = $this->createImageImage($sourceFile);
        $croppedImage = $this->crop($image, $this->width, $this->height, $this->forced);

        self::saveImage($croppedImage, $destinationFile, $this->quality);
    }

    /**
     * If cropping area goes out of image, jpg is converted to png to make transparent edges
     * @param string $file original file
     * @param string $ext original file extension
     * @return string
     */
    public function getNewExtension($sourceFile, $ext)
    {
        switch ($ext) {
            case 'png':
            case 'gif':
                return 'png';
                break;
            case 'jpeg':
            case 'jpg':
                if ($this->croppingGoesOutOfImage($sourceFile, $this->width, $this->height)) {
                    return 'png';
                } else {
                    return $ext;
                }
                break;
            default:
                return 'png';
        }

    }

    private function croppingGoesOutOfImage($sourceFile, $destWidth, $destHeight)
    {
        if (!$this->forced) {
            return false;
        }
        $imageInfo = getimagesize($sourceFile);
        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];
        $goesOut = $sourceWidth / $sourceHeight != $destWidth / $destHeight;
        return $goesOut;
    }

    public function crop($image, $widthDest, $heightDest, $forced)
    {
        $widthSource = imagesx($image);
        $heightSource = imagesy($image);

        $destProportion = $widthDest / $heightDest;
        $sourceProportion = (double)$widthSource / (double)$heightSource;


        if ($sourceProportion > $destProportion) {
            $widthDiff = 0;
            $heightDiff = ($heightDest - $widthDest/($sourceProportion))/2;
        } else {
            $widthDiff = ($widthDest - $heightDest*($sourceProportion))/2;
            $heightDiff = 0;
        }

        $startX = 0;
        $startY = 0;
        if ($forced) {
            if($heightDiff == 0 && $widthDiff != 0) {
                $startX = round(($widthDest - $heightDest * $sourceProportion) / 2);
            } elseif($heightDiff != 0 && $widthDiff == 0) {
                $startY = round(($heightDest - $widthDest / $sourceProportion) / 2);
            }
        } else {
            if($heightDiff == 0 && $widthDiff != 0) {
                $widthDest = $heightDest * $sourceProportion;
            } elseif($heightDiff != 0 && $widthDiff == 0) {
                $heightDest = $widthDest / $sourceProportion;
            }
        }

        $imageNew = imagecreatetruecolor($widthDest, $heightDest);
        imagealphablending($imageNew, false);
        imagesavealpha($imageNew,true);
        $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
        imagefilledrectangle ( $imageNew, 0, 0, $widthDest, $heightDest, $color );
        imagecopyresampled($imageNew, $image, $startX, $startY, 0, 0, $widthDest - $startX*2, $heightDest - $startY*2, $widthSource, $heightSource);
        return $imageNew;
    }


    private function resizeRequired($imageFile)
    {
        $imageInfo = getimagesize($imageFile);
        $widthS = $imageInfo[0];
        $heightS = $imageInfo[1];
        $widthT = $this->width;
        $heightT = $this->height;
        if ($this->forced) {
            return $widthS != $widthT || $heightS != $heightT;
        } else {
            return $widthS > $widthT || $heightS > $heightT;
        }
    }

}
