<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Content;


/**
 * Page class.<br />
 *
 * @package ImpressPages
 */

class Page extends \Ip\Page
{
    protected $dynamicModules;
    protected $linkIgnoreRedirect;

    public function getLink($ignoreRedirect = false)
    {
        if (ipIsManagementState()) {
            $ignoreRedirect = true;
        }


        if ($this->link == null || $this->linkIgnoreRedirect == null) {
            $this->generateDepthAndLink();
        }

        if ($ignoreRedirect) {
            return $this->linkIgnoreRedirect;
        } else {
            return $this->link;
        }
    }


    public function getDepth()
    {
        if ($this->depth == null) {
            $this->generateDepthAndLink();
        }

        return $this->depth;
    }


    public function getDynamicModules()
    {
        return $this->dynamicModules;
    }

    public function setDynamicModules($dynamicModules)
    {
        $this->dynamicModules = $dynamicModules;
    }

    private function generateDepthAndLink()
    {
        $tmpUrlVars = array();
        $tmpId = $this->getId();
        $element = DbFrontend::getPage($tmpId);
        while ($element['parent'] !== null) {
            $tmpUrlVars[] = $element['url'];
            $element = DbFrontend::getPage($element['parent']);
        }
        $languageId = DbFrontend::languageByRootPage($element['id']);

        $urlVars = array();

        for ($i = sizeof($tmpUrlVars) - 1; $i >= 0; $i--) // " - 1: eliminating invisible root content element"
        {
            $urlVars[] = $tmpUrlVars[$i];
        }

        $this->depth = sizeof($urlVars);

        switch ($this->type) {
            case 'subpage':
                $url = ipDb()->selectValue('page', 'url', array('parentId' => $this->id, 'isVisible' => 1), 'ORDER BY `pageOrder`');

                $this->link = \Ip\Internal\Deprecated\Url::generate($languageId, null, $url);

                break;
            case 'redirect':
                $this->link = $this->redirectUrl;
                break;
            case 'inactive':
            case 'default':
            default:
                $this->link = \Ip\Internal\Deprecated\Url::generate($languageId, $this->zoneName, $urlVars);
                break;
        }

        $this->linkIgnoreRedirect = \Ip\Internal\Deprecated\Url::generate($languageId, $this->zoneName, $urlVars);
    }


}




