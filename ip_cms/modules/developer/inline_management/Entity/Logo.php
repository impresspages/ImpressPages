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
    const TYPE_LOGO = 'logo';

    private $type;
    private $image;
    private $imageOrig;
    private $x1;
    private $y1;
    private $x2;
    private $y2;
    private $requiredWidth;

    /**
     * @param array|string $data
     */
    public function __construct($data)
    {
        global $parametersMod;
        if(!is_string($data)) {
            $data = $this->parseStr($data);
        }

        if (!isset($data['type'])) {
            $data['type'] = self::TYPE_TEXT;
        }

        switch($data['type']) {
            case self::TYPE_TEXT:
                $this->type = self::TYPE_TEXT;
                break;
            case self::TYPE_LOGO:
                $this->type = self::TYPE_LOGO;
                break;
            default:
                $this->type = self::TYPE_TEXT;
                break;
        }

        if (!empty($data['image']) && file_exists($data['image']) && !empty($data['imageOrig']) && file_exists($data['imageOrig']) ) {
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
                $this->requiredWidth = $data['requiredWidth'];
            }
        }

        if (!empty($data['text'])) {
            $this->text = $data['text'];
        } else {
            $this->text = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name');
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
        $data['requiredWidth'] = $this->requiredWidth;
        return json_encode(\Library\Php\Text\Utf8::checkEncoding($data));
    }


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

    public function getText()
    {
        return $this->text;
    }

    private function parseStr($str)
    {
        return json_decode($str);
    }

}