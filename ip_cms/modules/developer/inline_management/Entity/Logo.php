<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */


namespace Modules\developer\inline_management\Entity;


class Logo
{
    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';

    private $type;
    private $image;
    private $imageOrig;
    private $x1;
    private $y1;
    private $x2;
    private $y2;
    private $requiredWidth;
    private $requiredHeight;

    private $text;
    private $font;
    private $color;

    /**
     * @param array|string $data
     */
    public function __construct($data, $defaultLogo = null)
    {
        global $parametersMod;
        if(is_string($data)) {
            $data = $this->parseStr($data);
        }

        if (!isset($data['type'])) {
            if ($defaultLogo) {
                $data['type'] = self::TYPE_IMAGE;
            } else {
                $data['type'] = self::TYPE_TEXT;
            }
        }

        switch($data['type']) {
            case self::TYPE_TEXT:
                $this->type = self::TYPE_TEXT;
                break;
            case self::TYPE_IMAGE:
                $this->type = self::TYPE_IMAGE;
                break;
            default:
                $this->type = self::TYPE_TEXT;
                break;
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
            $this->image = $defaultLogo;
        }

        if (!empty($data['text'])) {
            $this->setText($data['text']);
        } else {
            $this->setText($parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name'));
        }

        if (isset($data['color'])) {
            $this->setColor($data['color']);
        }
        if (isset($data['font'])) {
            $this->setFont($data['font']);
        }

    }

    public function getValueStr()
    {
        $data = array();
        $data['type'] = $this->type;
        $data['image'] = $this->image;
        $data['imageOrig'] = $this->imageOrig;
        $data['x1'] = $this->x1;
        $data['y1'] = $this->y1;
        $data['x2'] = $this->x2;
        $data['y2'] = $this->y2;
        $data['text'] = $this->text;
        $data['color'] = $this->color;
        $data['font'] = $this->font;
        $data['requiredWidth'] = $this->requiredWidth;
        $data['requiredHeight'] = $this->requiredHeight;
        return json_encode(\Library\Php\Text\Utf8::checkEncoding($data));
    }

    //GETTERS

    public function getType()
    {
        return $this->type;
    }

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

    public function getText()
    {
        return $this->text;
    }

    public function getFont()
    {
        return $this->font;
    }

    public function getColor()
    {
        return $this->color;
    }

    //SETTERS

    public function setType($type)
    {
        $this->type = $type;
    }

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

    public function setText($text)
    {
        $this->text = $text;
    }

    public function setFont($font)
    {
        $this->font = $font;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }

    //---


    private function parseStr($str)
    {
        return json_decode($str, true);
    }

}