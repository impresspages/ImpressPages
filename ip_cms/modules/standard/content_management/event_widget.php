<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\content_management;
if (!defined('CMS')) exit;


class EventWidget extends \Ip\Event{

    private $widgets = array();

    public function addWidget($widget) {
        $this->widgets[$widget->getName()] = $widget;
    }

    public function removeWidget($name) {
        unset($this->widgets[$name]);
    }

    public function getWidget($name) {
        if (isset($this->widgets[$name])) {
            return $this->widgets[$name];
        } else {
            return false;
        }
    }

    public function issetWidget($name) {
        return isset($this->widgets[$name]);
    }

    public function getWidgets() {
        return $this->widgets;
    }

}