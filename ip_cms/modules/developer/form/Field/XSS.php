<?php
/**
 * @package ImpressPages
 *
 */

/**
 * 
 * Add this field to your form to prevent XSS.
 * It adds hidden field with security token.
 * Form class adds this field by default in constructor.
 * 
 * 
 * @author Mangirdas
 *
 */
namespace Modules\developer\form\Field;


class XSS extends Field{
    
    public function __construct($options) {
        parent::__construct($options);
        $this->addValidator('XSS');
    }
    
    public function render($doctype) {
        $session = \Ip\ServiceLocator::getSession();
        return '
<input '.$this->getAttributesStr($doctype).' class="'.implode(' ',$this->getClasses()).'" name="'.htmlspecialchars($this->getName()).'"  '.$this->getValidationAttributesStr($doctype).' type="hidden" value="'.addslashes($session->getSecurityToken()).'" />
';
    }
    
    public function getLayout() {
        return self::LAYOUT_BLANK;
    }
    
    public function getType() {
        return self::TYPE_SYSTEM;
    }
    
    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getTypeClass() {
        return 'XSS';
    }    
    
}