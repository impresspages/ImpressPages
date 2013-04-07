<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2012 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\developer\form\Field;


class File extends Field{
    
    public function __construct($options) {
        parent::__construct($options);
    }
    
    public function render($doctype) {
        $data = array (
            'attributesStr' => $this->getAttributesStr($doctype),
            'classes' => implode(' ',$this->getClasses())
        );

        $view = \Ip\View::create('../view/field/File.php', $data);

        return $view->render();

        //<input type="file"  name="'.htmlspecialchars($this->getName()).'" '.$this->getValidationAttributesStr($doctype).' value="'.htmlspecialchars($this->getDefaultValue()).'" />';
    }
    
    /**
    * CSS class that should be applied to surrounding element of this field. By default empty. Extending classes should specify their value.
    */
    public function getTypeClass() {
        return 'file';
    }


    /**
     * @param array $values all posted form values
     * @param string $valueKey this field name
     */
    public function getValueAsString($values, $valueKey) {
        var_dump($_FILES);
        echo $valueKey;
        if (isset($_FILES[$valueKey]["tmp_name"])) {
            return $_FILES[$valueKey]["tmp_name"];
        } else {
            return '';
        }
    }
}