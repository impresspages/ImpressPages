<?php
/**
 * @package   ImpressPages
 */

namespace Plugin\SimplePage;

/**
 * \Frontend\Element is a base class of a page. All pages in ImpressPages website
 * should be an objects that extend this class.
 *
 * This element class is a page that displays one record from the AddressBook plugin
 *
 *
 * @package ImpressPages
 */

class ControllerActionElement extends \Frontend\Element
{
    protected $controller;
    protected $action;

    public function __construct($element)
    {
        parent::__construct($element->getId(), $element->getZoneName());

        $this->controller = new \Plugin\HelloWorld\Controller();
        $this->action = 'indexPage';
    }

    /**
     * Generate content of this element (page). In this example: data of record from AddressBook plugin.
     * @see Frontend.Element::generateContent()
     * @return string HTML
     */
    public function generateContent()
    {
        return $this->controller->{$this->action}();
    }

    /**
     * Generate a link to this element. Here you can't controll the beginning of the URL (see http://www.impresspages.org/docs/core/url-structure/)
     * But you can add anything to the end of URL: http://www.example.com/en/zone-key/anything-you-like
     * But keep in mind, that "anything" should be meaningfull. Because zone class function "findElement" should be able
     * to uniquely identify this element by that "anything-you-like" part.
     *
     * In this case we will ad a record Id at the end of the url to identify this page easily.
     * Constructor of \Frontend\Element class requires to supply id of an element. So we are sure, that ID is already set and can be used.
     *
     * @see ip_cms/frontend/Frontend.Element::getLink()
     * @return string link
     */
    public function getLink()
    {
        global $site;
        //generateUrl is a standard function in ImpressPages used to generate urls. All url's in website should be generated using this function.
        //Here we ask to generate an URL to a current language, zone of this page and add an id of this element at the end.
        //Result will be simmilar to this: http://www.example.com/en/zone-key/126
        //id is set upon the creation of this object in zone object. So now we can be sure that it is set.
        $pages = $this->controller->pages();

        foreach ($pages as $urlPath => $pageInfo) {
            if ($this->action == $pageInfo['action']) {
                if (empty($urlPath)) {
                    return $site->generateUrl(null, $this->getZoneName());
                } else {
                    return $site->generateUrl(null, $this->getZoneName(), explode('/', $urlPath));
                }
            }
        }

        if ($this->action == 'index') {
            return $site->generateUrl(null, $this->getZoneName());
        } else {
            return $site->generateUrl(null, $this->getZoneName(), array($this->action));
        }
    }
}










