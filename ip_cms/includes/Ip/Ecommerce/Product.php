<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 JSC Apro Media.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Ip\Ecommerce;


/**
 *
 * General product class used across many plugins
 *
 */
class Product
{
    const TYPE_DOWNLOADABLE = 'downloadable';
    const TYPE_PHYSICAL = 'physical';
    const TYPE_VIRTUAL = 'virtual';

    /**
     * @var int in cents
     */
    private $price;
    /**
     * @var string currency code. Eg. EUR USD
     */
    private $currency;
    private $title;
    /**
     * @var string plane text description
     */
    private $description;
    private $module;
    private $id;
    /**
     * @var int $weight in grams (1000 grams = 1 kilo)
     */
    private $weight;
    private $stockCount;
    /**
     * @var int (millimeters)
     */
    private $width;
    /**
     * @var int (millimeters)
     */
    private $height;
    /**
     * @var int (millimeters)
     */
    private $depth;
    /**
     * @var ProductOption[]
     */
    private $options;
    private $url;
    /**
     * @var string absolute url to image. Could be remote domain.
     */
    private $image;
    private $type;


    /**
     * @param string $module
     * @param string $id
     * @param int $type
     * @param string $title
     * @param int $price in cents
     * @param string $currency
     * @param array $options
     */
    public function __construct($module, $id, $type, $title, $price, $currency)
    {
        $this->setModule($module);
        $this->setId($id);
        $this->setType($type);
        $this->setTitle($title);
        $this->setPrice($price);
        $this->setCurrency($currency);

    }

    /**
     * @return array()
     */
    public function extract()
    {
        $answer =  array(
            'class' => get_class($this),
            'price' => $this->price,
            'currency' => $this->currency,
            'title' => $this->title,
            'description' => $this->description,
            'module' => $this->module,
            'id' => $this->id,
            'weight' => $this->weight,
            'stockCount' => $this->stockCount,
            'width' => $this->width,
            'height' => $this->height,
            'depth' => $this->depth,
            'url' => $this->url,
            'image' => $this->image,
            'type' => $this->type
        );

        $options = array();
        if ($this->getOptions()){
            foreach($this->options as $option) {
                $options[$option->getKey()] = $option->getValue();
            }
        }
        $answer['options'] = $options;
        return $answer;

    }


    /**
     * @return string three letters currency code
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency three letter currency code
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }


    /**
     * get price in cents
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price in cents
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function setModule($module)
    {
        $this->module = $module;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int (grams)
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight (grams)
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    public function getStockCount()
    {
        return $this->stockCount;
    }

    public function setStockCount($stockCount)
    {
        $this->stockCount = $stockCount;
    }

    /**
     * @return int (millimeters)
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width (millimeters)
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int (millimeters)
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height (millimeters)
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int (millimeters)
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param int $depth (millimeters)
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    /**
     * @return ProductOption[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    public function getOption($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }
        return null;
    }

    /**
     * @param ProductOption $option
     */
    public function addOption($option)
    {
        $this->options[$option->getKey()] = $option;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getImage(){
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type int
     * @throws CoreException
     */
    public function setType($type)
    {
        switch($type) {
            case self::TYPE_DOWNLOADABLE:
            case self::TYPE_PHYSICAL:
            case self::TYPE_VIRTUAL:
                $this->type = $type;
            break;
            default:
                throw new CoreException ("Unknown product type: ".$type, \Ip\CoreException::ECOMMERCE);
                break;
        }
    }
}
