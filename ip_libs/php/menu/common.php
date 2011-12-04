<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

/**
 * Common class to generate site menus. You can write your own more specific class by this example, but this one should enough in many cases.
 * @package Library
 */

namespace Library\Php\Menu;

if (!defined('CMS')) exit;


class Common {

    /**
     * Generates menu by specified zone name. You can specify start depth and depth limit like in MySQL. So, if you write generate('left_menu', 2, 3),
     * will be generated these menu levels: 2, 3, 4. And in this situation the function will generate empty string until the page on first level will be selected.
     * @param string $zoneName
     * @param int $startDepth if specified, will be returned only childs of this element
     * @param int $depthLimit option to limit the depth of menu
     * @return string HTML
     */
    static function generate($zoneName, $startDepth=1, $depthLimit=1000) {
        global $site;

        //variable check
        if($startDepth < 1) {
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('Start depth can\'t be less than one. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('Start depth can\'t be less than one.');
            return;
        }

        if($depthLimit < 1) {
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('Depth limit can\'t be less than one. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('Depth limit can\'t be less than one.');
            return;
        }
        //end variable check

        $answer = '';
        $menu =  $site->getZone($zoneName);
        if($menu) {
            $breadcrumb = $menu->getBreadcrumb();
            if($startDepth == 1) {
                $elements = $menu->getElements(); //get first level elements
            } elseif (isset($breadcrumb[$startDepth-2])) { // if we need a second level (2), we need to find a parent element at first level. And he is at position 0. This is where -2 comes from.
                $elements = $menu->getElements(null, $breadcrumb[$startDepth-2]->getId());
            }
            if(isset($elements) && sizeof($elements) > 0) {
                $curDepth = $elements[0]->getDepth();
                $maxDepth = $curDepth + $depthLimit - 1;
                $subElementsData = self::getSubElementsData($elements, $zoneName, $maxDepth, $curDepth);
                $html = $subElementsData['html'];
                $answer .= $html;
            }
        }else {
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && isset($backtrace[0]['line']))
            trigger_error('Undefined zone. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
        }



        return $answer;
    }


    /**
     * Generates submenu by specified parent element id.
     * @param string $zoneName
     * @param int $parentElementId if specified, will be returned only childs of this element
     * @param int $depthLimit option to limit the depth of menu
     * @return string HTML
     */
    static function generateSubmenu($zoneName, $parentElementId = null, $depthLimit = 1000) {
        global $site;

        //variable check
        if($depthLimit < 1) {
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
            trigger_error('Depth limit can\'t be less than one. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
            else
            trigger_error('Depth limit can\'t be less than one.');
            return;
        }
        //end variable check


        $answer = '';
        $menu =  $site->getZone($zoneName);
        if($menu) {
            $elements = $menu->getElements(null, $parentElementId);
            if($elements && sizeof($elements) > 0) {
                $curDepth = $elements[0]->getDepth();
                $maxDepth = $curDepth + $depthLimit - 1;
                $subElementsData = self::getSubElementsData($elements, $zoneName, $maxDepth, $curDepth);
                $html = $subElementsData['html'];
                $answer .= $html;
            }
        }else {
            $backtrace = debug_backtrace();
            if(isset($backtrace[0]['file']) && isset($backtrace[0]['line']))
            trigger_error('Undefined zone. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
        }

        return $answer;
    }





    /**
     * @param array $elements zone elements
     * @param string $zoneName
     * @param int $depth
     * @param int $inactiveDepth
     * @param bool $inactiveIfParent
     * @param int $curDepth used for recursion
     * @return string html of menu
     */
    static function getSubElementsData($elements, $zoneName, $depth, $curDepth) {
        global $site;
        $html = "\n";
        $html .= "<ul class=\"level".$curDepth."\">"."\n";

        $selected = false;
        foreach($elements as $key => $element) {
            $subHtml = '';
            $subSelected = false;
            if($curDepth < $depth) {
                $menu = $site->getZone($zoneName);
                $children = $menu->getElements(null, $element->getId());
                if(sizeof($children) > 0) {
                    $subElementsData = self::getSubElementsData($children, $zoneName, $depth, $curDepth+1);
                    $subHtml = $subElementsData['html'];
                    $subSelected = $subElementsData['selected'];
                }
            }

            $class = '';
            if($element->getCurrent()  || $element->getType() == 'redirect' && $element->getLink() == $site->getCurrentUrl()) {
                $class .= 'current';
                $selected = true;
            } elseif($element->getSelected() || $subSelected || $element->getType() == 'redirect' && self::existInBreadcrumb($element->getLink())) {
                // || $element->getLink() != '' && strpos($site->getCurrentUrl(), $element->getLink()) === 0 || $element->getLink().'/?cms_action=manage' == $site->getCurrentUrl()
                $class .= $class == '' ? 'selected' : 'Selected';
                $selected = true;
            }

            if($curDepth < $depth && sizeof($children) > 0)
            $class .= $class == '' ? 'subnodes' : 'Subnodes';

            if($class != '') {
                $class .= ' type'.ucwords($element->getType());
            } else {
                $class .= 'type'.ucwords($element->getType());
            }

            $tmpLink = $element->getLink();
            if ($tmpLink) {
                if($element->getType() == 'inactive') {
                    $html .= '  <li class="'.$class.'"><a class="'.$class.'" title="'.htmlspecialchars($element->getPageTitle()).'">'.htmlspecialchars($element->getButtonTitle()).'</a>'.$subHtml."</li>"."\n";
                } else {
                    $html .= '  <li class="'.$class.'"><a class="'.$class.'" href="'.$tmpLink.'"  title="'.htmlspecialchars($element->getPageTitle()).'">'.htmlspecialchars($element->getButtonTitle()).'</a>'.$subHtml."</li>"."\n";
                }
            } else {
                $html .= '  <li class="'.$class.'"><a>'.htmlspecialchars($element->getButtonTitle())."</a>\n".$subHtml."\n  </li>\n";
            }


        }
        $html .= "</ul>"."\n";

        $answer = array(
      'html' => $html,
      'selected' => $selected
        );
        return $answer;
    }


    private static function existInBreadcrumb($link) {
        global $site;
        $breadcrumb = $site->getBreadcrumb();
        array_pop($breadcrumb);
        foreach($breadcrumb as $key => $element) {
            if($element->getLink() == $link) {
                return true;
            }
        }
        return false;
    }

}


