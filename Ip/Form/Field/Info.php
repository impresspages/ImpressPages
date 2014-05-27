<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;


use Ip\Form\Field;

/**
 * Info field. When you want to output information and no actual input field.
 * @package Ip\Form\Field
 */
class Info extends Field
{

    protected $html;

    public function __construct($options = array()) {
        parent::__construct($options);

        if (!empty($options['html'])) {
            $this->setHtml($options['html']);
        }
    }

    public function render($doctype, $environment)
    {
        return $this->getHtml();
    }

    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getTypeClass()
    {
        return 'info';
    }

    public function setHtml($html)
    {
        $this->html = $html;
    }

    public function getHtml()
    {
        return $this->html;
    }

}
