<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */

 
namespace Modules\catalog\zone;  
  
if (!defined('FRONTEND')&&!defined('BACKEND')) exit; 
 
 
class Module{

  /**
   * Generates menu by specified zone name. You can specify start depth and depth limit like in MySQL. So, if you write generate('left_menu', 2, 3),
   * will be generated these menu levels: 2, 3, 4. And in this situation the function will generate empty string until the page on first level will be selected.
   * @param string $zoneName
   * @param int $startDepth if specified, will be returned only childs of this element
   * @param int $depthLimit option to limit the depth of menu
   * @return string HTML    
   */   
  public static function generateMenu($zoneName, $startDepth=1, $depthLimit=1000){
    global $site;

    //variable check    
    if($startDepth < 1)
    {
      $backtrace = debug_backtrace();
      if(isset($backtrace[0]['file']) && $backtrace[0]['line'])
       trigger_error('Start depth can\'t be less than one. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ');
      else
       trigger_error('Start depth can\'t be less than one.');
      return;
    }
    
    if($depthLimit < 1)
    {
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
    if($menu){
      $elements = array();
      $breadcrumb = $menu->getBreadcrumb();
      if($startDepth == 1) {
        $elements = $menu->getElements(null, null, 0, null, false, true, false); //get first level elements
      } elseif (isset($breadcrumb[$startDepth-2])) { // if we need a second level (2), we need to find a parent element at first level. And he is at position 0. This is where -2 comes from.
        $elements = $menu->getElements(null, $breadcrumb[$startDepth-2]->getId(), 0, null, false, true, false);
      }
      $items = array();
      foreach($elements as $element){
        if(true||$element->getId()%10 === 0){ //if category
          $items[] = $element;        
        }
      }
      if(isset($items) && sizeof($items) > 0){
        $curDepth = $items[0]->getDepth();
        $maxDepth = $curDepth + $depthLimit - 1;
        $html = self::addElements($items, $zoneName, $maxDepth, $curDepth);
        $answer .= $html;
      }
    }else{
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
  public static function generateSubmenu($zoneName, $parentElementId = null, $depthLimit = 1000){
    global $site;
    //variable check    
    if($depthLimit < 1)
    {
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
    if($menu){
      $elements = $menu->getElements(null, $parentElementId, 0, null, false, true, false);

      $items = array();
      foreach($elements as $element){
        if($element->getId()%10 === 0){ //if category
          $items[] = $element;        
        }
      }        
      
      if($items && sizeof($items) > 0){
        $curDepth = $items[0]->getDepth();
        $maxDepth = $curDepth + $depthLimit - 1;
        
        $html = self::addElements($items, $zoneName, $maxDepth, $curDepth);
        $answer .= $html;
      }
    }else{
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
  protected static function addElements($elements, $zoneName, $depth, $curDepth){
    global $site;
    $html = '';
    $html .= "<ul class=\"level".$curDepth."\">\n";
      
      foreach($elements as $key => $element){   
        $subHtml = '';
        if($curDepth < $depth){
          $menu = $site->getZone($zoneName);
          $children = $menu->getElements(null, $element->getId(), 0, null, false, true, false);
          
          $items = array();
          foreach($children as $child){
            if($child->getId()%10 === 0){ //if category
              $items[] = $child;        
            }
          }             
          if(sizeof($items) > 0)
            $subHtml = self::addElements($items, $zoneName, $depth, $curDepth+1);
        }
        
        $class = '';
        if($element->getCurrent())
          $class .= 'current';
        elseif($element->getSelected())
          $class .= $class == '' ? 'selected' : 'Selected';
          
        if($curDepth < $depth && sizeof($items) > 0)
          $class .= $class == '' ? 'subnodes' : 'Subnodes';
  
        $tmpLink = $element->getLink();
        if ($tmpLink) {
          $html .= '<li class="'.$class.'"><a class="'.$class.'" href="'.$tmpLink.'"  title="'.htmlspecialchars($element->getPageTitle()).'">'.htmlspecialchars($element->getButtonTitle()).'</a>'.$subHtml."</li>\n";
        } else {
          $html .= '<li class="'.$class.'"><a>'.htmlspecialchars($element->getButtonTitle()).$subHtml."</a></li>\n";
        }
          
        
      }
    $html .= "</ul>\n";
    return $html;
  }
  

}


