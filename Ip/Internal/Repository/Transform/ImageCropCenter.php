<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace Ip\Internal\Repository\Transform;

class ImageCropCenter extends Image
{
    protected $widthDest;
    protected $heightDest;
    protected $quality;
    protected $version = 3; //to make fingerprint change when algorithm changes.

    /**
     * @param int $x1 left top coordinate of source
     * @param int $y1 left top coordinate of source
     * @param int $x2 right bottom coordinate of source
     * @param int $y2 right bottom coordinate of source
     * @param int $widthDest required width of detination image
     * @param int $heightDest required height of detination image
     * @param null $quality
     */
    public function __construct($widthDest, $heightDest, $quality = null)
    {
        if ($heightDest <= 0 || $widthDest <= 0) {
            throw new \Ip\Exception\Repository\Transform("Incorrect width or height");
        }
        if ($quality === null) {
            $quality = ipGetOption('Config.defaultImageQuality');
        }
        $this->widthDest = $widthDest;
        $this->heightDest = $heightDest;
        $this->quality = (int)$quality;
    }

    /**
     * @param string $sourceFile
     * @param string $destinationFile
     * @return void
     */
    public function transform($sourceFile, $destinationFile)
    {
        //check if modifications are needed
        if (!$this->resizeRequired($sourceFile)) {
            copy($sourceFile, $destinationFile);
            return;
        }


        $imageInfo = getimagesize($sourceFile);
        $widthSource = $imageInfo[0];
        $heightSource = $imageInfo[1];

        $sourceProportions = $widthSource / $heightSource;
        $destinationProportions = $this->widthDest / $this->heightDest;
        if ($sourceProportions > $destinationProportions) {
            //cut left and right edges
            $x1 = round($widthSource / 2 - $heightSource * $destinationProportions / 2);
            $x2 = round($widthSource / 2 + $heightSource * $destinationProportions / 2);
            $y1 = 0;
            $y2 = $heightSource;
        } else {
            //cut top and bottom edges
            $x1 = 0;
            $x2 = $widthSource;
            $y1 = round($heightSource / 2 - $widthSource / $destinationProportions / 2);
            $y2 = round($heightSource / 2 + $widthSource / $destinationProportions / 2);
        }


        //modify image
        $croppedImage = $this->crop(
            $sourceFile,
            $x1,
            $y1,
            $x2,
            $y2,
            $this->widthDest,
            $this->heightDest
        );

        self::saveImage($croppedImage, $destinationFile, $this->quality);
    }


    public function crop($sourceFile, $x1, $y1, $x2, $y2, $widthDest, $heightDest)
    {
        $image = $this->createImageImage($sourceFile);


        if ($widthDest === null) {
            $widthDest = $x2 - $x1;
        }
        if ($heightDest === null) {
            $heightDest = $y2 - $y1;
        }

        if ($heightDest <= 0 || $widthDest <= 0) {
            throw new \Ip\Exception\Repository\Transform("Incorrect width or height");
        }

        if ($this->croppingGoesOutOfImage(
            $sourceFile,
            $x1,
            $y1,
            $x2,
            $y2
        )
        ) { //cropping area goes out of image edge. Fill transparent.
            /*
            * Negative coordinates x1, y1 are possible.
            * This part of code just adds transparent edges in this way making $image required proportions.
            * We don't care about the size in this step.
            *
            * */
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

        } else {
            $sx1 = $x1;
            $sx2 = $x2;
            $sy1 = $y1;
            $sy2 = $y2;
        }

        /**
         * Our $image is required proportions. The only thing we need to do is to scale the image and save.
         */

        $imageNew = $this->createEmptyImage($widthDest, $heightDest);
        imagealphablending($imageNew, false);
        imagesavealpha($imageNew, true);
        $color = imagecolorallocatealpha($imageNew, 255, 255, 255, 127);
        imagefilledrectangle($imageNew, 0, 0, $widthDest, $heightDest, $color);
        imagecopyresampled($imageNew, $image, 0, 0, $sx1, $sy1, $widthDest, $heightDest, $sx2 - $sx1, $sy2 - $sy1);

        return $imageNew;
    }


    private function croppingGoesOutOfImage($sourceFile, $x1, $y1, $x2, $y2)
    {
        $imageInfo = getimagesize($sourceFile);
        $widthSource = $imageInfo[0];
        $heightSource = $imageInfo[1];
        $goesOut = $x2 - $x1 > $widthSource || $y2 - $y1 > $heightSource || $x1 < 0 || $y1 < 0;
        return $goesOut;
    }


    private function resizeRequired($imageFile)
    {
        $imageInfo = getimagesize($imageFile);
        $widthS = $imageInfo[0];
        $heightS = $imageInfo[1];
        $widthT = $this->widthDest;
        $heightT = $this->heightDest;

        $resizeRequired = $widthS != $widthT || $heightS != $heightT;

        return $resizeRequired;

    }

}
