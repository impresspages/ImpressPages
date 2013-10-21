<?php
    /**
     * @package   ImpressPages
     *
     *
     */

namespace Ip\Ecommerce;


/**
 *
 * Product option class. Eg. clothes size (S, M, L), or color (green, yellow, blue), etc.
 *
 * If you are creating plugin supply products with options, you likely
 * want to extend this class and overwrite methods getKeyTitle, getValueTitle to return human readable and translated value
 * You can extend constructor too.
 */
abstract class ProductOption
{
    /**
     * @var string
     */
    protected $key;
    /**
     * @var string
     */
    protected $value;

    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Extract object values to associative array.
     * Marked as final because it can't change. Result of this function is being stored to database.
     * It should always have the same structure.
     * @return array
     */
    final function extract()
    {
        $answer = array(
            'class' => get_class($this),
            'key' => $this->getKey(),
            'value' => $this->getValue(),
            'keyTitle' => $this->getKeyTitle(),
            'valueTitle' => $this->getValueTitle()
        );
        return $answer;
    }

    /**
     * Human readable title of option
     * @return string
     */
    public abstract function getKeyTitle();

    /**
     * Human readable representation of value
     * @return string
     */
    public abstract function getValueTitle();

}