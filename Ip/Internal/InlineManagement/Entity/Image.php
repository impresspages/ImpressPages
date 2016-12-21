<?php
/**
 * @package ImpressPages
 *
 */


namespace Ip\Internal\InlineManagement\Entity;


class Image
{
    private $imageOrig;
    private $x1;
    private $y1;
    private $x2;
    private $y2;
    private $requiredWidth;
    private $requiredHeight;
    /** @var int randomly created hopefully unique id */
    private $id;

    /**
     * @param array|string $data
     * @param null $defaultImage
     */
    public function __construct($data, $defaultImage = null)
    {
        if (is_string($data)) {
            $data = $this->parseStr($data);
        }

        if (!empty($data['imageOrig']) && file_exists(ipFile('file/repository/' . $data['imageOrig']))) {
            $this->imageOrig = $data['imageOrig'];

            if (isset($data['x1']) && isset($data['y1']) && isset($data['x2']) && isset($data['y2'])) {
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

                $quality = null;
                $quality = ipGetOption('Config.slotImageQuality');
                if (!$quality) {
                    $quality = ipGetOption('Config.defaultImageQuality');
                }

                $transform = array(
                    'type' => 'crop',
                    'x1' => $this->getX1(),
                    'y1' => $this->getY1(),
                    'x2' => $this->getX2(),
                    'quality' => $quality,
                    'y2' => $this->getY2(),
                    'width' => $this->getRequiredWidth(),
                    'height' => $this->getRequiredHeight()
                );
                $this->image = ipFileUrl(ipReflection($this->getImageOrig(), $transform, null));


            }
        } else {
            $this->image = $defaultImage;
        }
        if (!empty($data['id'])) {
            $this->id = $data['id'];
        } else {
            $this->id = mt_rand(2, 2147483647); //1 used for inline logo
        }
    }

    public function getValueStr()
    {
        $data = [];
        $data['imageOrig'] = $this->imageOrig;
        $data['x1'] = $this->x1;
        $data['y1'] = $this->y1;
        $data['x2'] = $this->x2;
        $data['y2'] = $this->y2;
        $data['requiredWidth'] = $this->requiredWidth;
        $data['requiredHeight'] = $this->requiredHeight;
        $data['id'] = $this->id;
        return json_encode(\Ip\Internal\Text\Utf8::checkEncoding($data));
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

    public function getId()
    {
        return $this->id;
    }


    //SETTERS

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
