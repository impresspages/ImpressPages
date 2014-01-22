<?php
/**
 * @package ImpressPages
 *
 */

/**
 *
 * Add this field to your form to prevent CSRF attacks.
 * It adds hidden field with security token.
 * Form class adds this field by default in constructor.
 *
 */
namespace Ip\Form\Field;


class Csrf extends Blank{

    public function __construct($options = array()) {
        parent::__construct($options);
        $this->addValidator('Csrf');
    }

    public function render($doctype, $environment) {
        $session = \Ip\ServiceLocator::application();
        return '
            <input '.$this->getAttributesStr($doctype).' class="'.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'"  '.$this->getValidationAttributesStr($doctype).' type="hidden" value="'.addslashes($session->getSecurityToken()).'" />
        ';
    }

    public function getType() {
        return self::TYPE_SYSTEM;
    }

}
