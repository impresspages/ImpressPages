<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\standard\content_management;


if (!defined('CMS')) exit;

require_once (__DIR__.'/site_db.php');

/**
 * Website zone element. Typically each element represents one page on zone.<br />
 *
 * @package ImpressPages
 */

class Element extends \Frontend\Element {
    protected $dynamicModules;
    protected $linkIgnoreRedirect;

    public function getLink($ignoreRedirect = false) {
        global $site;
        if ($site->managementState()) {
            $ignoreRedirect = true;
        }
        
        
        if($this->link == null || $this->linkIgnoreRedirect == null) {
            $this->generateDepthAndLink();
        }

        if($ignoreRedirect)
        {
            return $this->linkIgnoreRedirect;
        }
        else
        {
            return $this->link;
        }
    }



    public function getDepth() {
        if($this->depth == null)
        $this->generateDepthAndLink();

        return $this->depth;
    }






    public function getDynamicModules() {
        return $this->dynamicModules;
    }

    public function setDynamicModules($dynamicModules) {
        $this->dynamicModules=$dynamicModules;
    }

    private function generateDepthAndLink() {
        global $site;
        $tmpUrlVars = array();
        $tmpId = $this->getId();
        $element = DbFrontend::getElement($tmpId);
        while($element['parent'] !== null) {
            $tmpUrlVars[] = $element['url'];
            $element = DbFrontend::getElement($element['parent']);
        }
        $languageId = DbFrontend::languageByRootElement($element['id']);

        $urlVars = array();

        for($i=sizeof($tmpUrlVars)-1; $i >= 0; $i--) // " - 1: eliminating invisible root content element"
        {
            $urlVars[] = $tmpUrlVars[$i];
        }

        $this->depth = sizeof($urlVars);

        switch($this->type) {
            case 'subpage':
                $tmpChildren = $site->getZone($this->zoneName)->getElements($languageId, $this->id, 0, $limit = 1);
                if(sizeof($tmpChildren) == 1)
                $this->link = $tmpChildren[0]->getLink();
                else
                $this->link = $site->generateUrl($languageId, $this->zoneName, $urlVars);  //open current page if no subpages exist
                break;
            case 'redirect':
                if($site->managementState()) {
                    if(strpos($this->redirectUrl, BASE_URL) === 0) {
                        if(strpos($this->redirectUrl, 'cms_action=manage') === false) {
                            if(strpos($this->redirectUrl, '?') === false) {
                                $this->redirectUrl .= '?cms_action=manage';
                            } else {
                                $this->redirectUrl .= '&cms_action=manage';
                            }
                        }
                    }
                }
                $this->link = $this->redirectUrl;
                break;
            case 'inactive':
            case 'default':
            default:
                $this->link = $site->generateUrl($languageId, $this->zoneName, $urlVars);
                break;
        }

        $this->linkIgnoreRedirect = $site->generateUrl($languageId, $this->zoneName, $urlVars);
    }



}




