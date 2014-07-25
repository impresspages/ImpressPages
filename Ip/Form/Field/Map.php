<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Form\Field;

use Ip\Form\Field;


class Map extends Field
{

    /**
     * Render field
     *
     * @param string $doctype
     * @param $environment
     * @return string
     */
    public function render($doctype, $environment)
    {
        $data = array(
            'attributesStr' => $this->getAttributesStr($doctype),
            'classes' => implode(' ', $this->getClasses()),
            'inputName' => $this->getName()
        );

        if ($environment == \Ip\Form::ENVIRONMENT_ADMIN) {
            $viewFile = 'adminView/map.php';
        } else {
            $viewFile = 'publicView/map.php';
        }
        $view = ipView($viewFile, $data);

        return $view->render();

//        return '<div ' . $this->getAttributesStr($doctype) . ' class="form-control ' . implode(
//            ' ',
//            $this->getClasses()
//        ) . '"   ></div>
//        <input name="' . esc($this->getName(), 'attr') . '[\'\']" type="hidden" value="' . htmlspecialchars($this->getValue()) . '" ' . $this->getValidationAttributesStr(
//            $doctype
//        ) . '/>
//        ';
    }

    /**
     * Get class type
     *
     * CSS class that should be applied to surrounding element of this field.
     * By default empty. Extending classes should specify their value.
     * @return string
     */
    public function getTypeClass()
    {
        return 'map';
    }

}


