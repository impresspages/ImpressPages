<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace Ip\Internal\Repository\Transform;

class ImageWidth extends Image
{
    protected $width;
    protected $quality;
    protected $forced;

    /**
     * @param int $width width of area where image should fit
     * @param int $quality image quoality from 0 to 100
     * @param bool $forced if true, and supplied image has different proportions, resulting image will have white edges to make image exactly $Width x $height
     */
    public function __construct($width, $quality = null, $forced = false)
    {
        if ($quality === null) {
            $quality = ipGetOption('Config.defaultImageQuality');
        }

        $this->width = $width;
        $this->quality = (int)$quality;
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
        $croppedImage = $this->crop($image, $this->width, $this->forced);

        self::saveImage($croppedImage, $destinationFile, $this->quality);
    }


    public function crop($image, $widthDest, $forced)
    {
        if ($widthDest <= 0) {
            throw new \Ip\Exception\Repository\Transform("Incorrect width or height");
        }
        $widthSource = imagesx($image);
        $heightSource = imagesy($image);

        $sourceProportion = (double)$widthSource / (double)$heightSource;

        if (!$forced && $widthSource < $widthDest) {
            $widthDest = $widthSource;
        }

        $heightDest = round($widthDest / $sourceProportion);



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
            0,
            0,
            $widthDest,
            $heightDest,
            $widthSource,
            $heightSource
        );
        return $imageNew;
    }


    private function resizeRequired($imageFile)
    {
        $imageInfo = @getimagesize($imageFile);
        $widthS = $imageInfo[0];
        if ($widthS > $this->width || $widthS < $this->width && $this->forced) {
            return true;
        } else {
            return false;
        }
    }

}
