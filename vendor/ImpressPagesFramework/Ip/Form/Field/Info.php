<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


/**
 * Info field
 *
 * When you want to output information and no actual input field.
 */
class Info extends Field
{

    protected $html;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        if (!empty($options['html'])) {
            $this->setHtml($options['html']);
        }
    }

    /**
     * Render field
     *
     * @param string $doctype
     * @param $environment
     * @return string
     */
    public function render($doctype, $environment)
    {
        return $this->getHtml();
    }



    /**
     * Set HTML
     *
     * @param string $html
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }

    /**
     * Get HTML
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

}
