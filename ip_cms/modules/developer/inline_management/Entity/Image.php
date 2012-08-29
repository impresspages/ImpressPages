<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */


namespace Modules\developer\inline_management\Entity;


class Image
{
    private $image;
    private $imageOrig;
    private $x1;
    private $y1;
    private $x2;
    private $y2;
    private $requiredWidth;
    private $requiredHeight;

    /**
     * @param array|string $data
     */
    public function __construct($data, $defaultImage = null)
    {
        if(is_string($data)) {
            $data = $this->parseStr($data);
        }

        if (!empty($data['image']) && file_exists(BASE_DIR.$data['image']) && !empty($data['imageOrig']) && file_exists(BASE_DIR.$data['imageOrig']) ) {
            $this->image = $data['image'];
            $this->imageOrig = $data['imageOrig'];

            if (isset($data['x1']) && isset($data['y1']) && isset($data['x2']) && isset($data['y2']) ) {
                $this->x1 = $data['x1'];
                $this->y1 = $data['y1'];
                $this->x2 = $data['x2'];
                $this->y2 = $data['y2'];

                if (empty($data['requiredWidth'])) {
                    $data['requiredWidth'] = $this->x2 - $this->x1;
                }
                if (empty($data['requiredHeight'])) {
                    $data['requiredHeight'] = $this->y2 - $this->y1;
                }

                $this->requiredWidth = $data['requiredWidth'];
                $this->requiredHeight = $data['requiredHeight'];
            }
        } else {
            $this->image = $defaultImage;
        }
    }

    public function getValueStr()
    {
        $data = array();
        $data['image'] = $this->image;
        $data['imageOrig'] = $this->imageOrig;
        $data['x1'] = $this->x1;
        $data['y1'] = $this->y1;
        $data['x2'] = $this->x2;
        $data['y2'] = $this->y2;
        $data['requiredWidth'] = $this->requiredWidth;
        $data['requiredHeight'] = $this->requiredHeight;
        return json_encode(\Library\Php\Text\Utf8::checkEncoding($data));
    }

    //GETTERS

    public function getImage()
    {
        return $this->image;
    }

    public function getImageOrig()
    {
        return $this->imageOrig;
    }

    public function getX1()
    {
        return $this->x1;
    }

    public function getY1()
    {
        return $this->y1;
    }

    public function getX2()
    {
        return $this->x2;
    }

    public function getY2()
    {
        return $this->y2;
    }

    public function getRequiredWidth()
    {
        return $this->requiredWidth;
    }

    public function getRequiredHeight()
    {
        return $this->requiredHeight;
    }


    //SETTERS

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function setImageOrig($imageOrig)
    {
        $this->imageOrig = $imageOrig;
    }

    public function setX1($x1)
    {
        $this->x1 = $x1;
    }

    public function setY1($y1)
    {
        $this->y1 = $y1;
    }

    public function setX2($x2)
    {
        $this->x2 = $x2;
    }

    public function setY2($y2)
    {
        $this->y2 = $y2;
    }

    public function setRequiredWidth($requiredWidth)
    {
        $this->requiredWidth = $requiredWidth;
    }

    public function setRequiredHeight($requiredHeight)
    {
        $this->requiredHeight = $requiredHeight;
    }


    //---


    private function parseStr($str)
    {
        return json_decode($str, true);
    }

}