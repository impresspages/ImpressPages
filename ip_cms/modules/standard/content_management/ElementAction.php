<?php
/**
 * @package   ImpressPages
 */

namespace Modules\standard\content_management;


class ElementAction extends Element {

    protected $action;

    public function hydrate($dbElement){
        parent::hydrate($dbElement);

        $this->action = $dbElement['action'];
    }

    /**
     * Generate content of this element (page). In this example: data of record from AddressBook plugin.
     * @see Frontend.Element::generateContent()
     * @return string HTML
     */
    public function generateContent()
    {
        $site = \Ip\ServiceLocator::getSite();
        $controllerInfo = $site->parseControllerAction($this->action, 'SiteController');

        if (!class_exists($controllerInfo['controller'])) {
            throw new \Ip\CoreException('Requested controller doesn\'t exist');
        }

        $controller = new $controllerInfo['controller']();
        return $controller->$controllerInfo['action']();
    }
}