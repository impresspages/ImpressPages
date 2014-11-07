<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;


/**
 * Add this field to your form to prevent CSRF attacks.
 * It adds hidden field with security token.
 * Form class adds this field by default in constructor.
 */
class Csrf extends Blank
{

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->addValidator('Csrf');
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
        $session = \Ip\ServiceLocator::application();

        return '
            <input ' . $this->getAttributesStr($doctype) . ' class="' . implode(
            ' ',
            $this->getClasses()
        ) . '" name="' . htmlspecialchars($this->getName()) . '"  ' . $this->getValidationAttributesStr(
            $doctype
        ) . ' type="hidden" value="' . addslashes($session->getSecurityToken()) . '" />
        ';
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_SYSTEM;
    }

    public function getLayout()
    {
//        return self::LAYOUT_BLANK;
    }

}
