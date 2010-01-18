<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see license.html
 */
namespace Modules\catalog\items;

if (!defined('BACKEND')) exit;

require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/std_mod.php');

class ElementUrlLang extends \Modules\developer\std_mod\ElementTextLang{
  

  
  function processInsert($prefix, $lastInsertId, $area){
    $languages = \Frontend\Db::getLanguages(true);
    
    foreach($languages as $key => $language){
      $url = $_REQUEST[$prefix.'_'.$language['id']];
      if ($url != '') {//empty urls are filled in on "afterInsert/afterUpdate"
        if(\Modules\catalog\items\Db::getItemByUrl($language['id'], $url) || \Modules\catalog\categories\Db::getCategoryByUrl($language['id'], $url)){
          $i = 1;
          while(\Modules\catalog\items\Db::getItemByUrl($language['id'], $url.$i) || \Modules\catalog\categories\Db::getCategoryByUrl($language['id'], $url.$i)){
            $i++;
          }
          $_REQUEST[$prefix.'_'.$language['id']] = $url.$i;     
        }       
      }            
    
    }
    
    parent::processInsert($prefix, $lastInsertId, $area);
  }  
  
  
  function processUpdate($prefix, $rowId, $area){
    $languages = \Frontend\Db::getLanguages(true);
        
    foreach($languages as $key => $language){
      $url = $_REQUEST[$prefix.'_'.$language['id']];
      if ($url != '') {//empty urls are filled in on "afterInsert/afterUpdate"
        $item = \Modules\catalog\items\Db::getItemByUrl($language['id'], $url);
        $category = \Modules\catalog\categories\Db::getCategoryByUrl($language['id'], $url);
        if($item && $item['record_id'] != $rowId || $category){
          $i = 1;
          $item = \Modules\catalog\items\Db::getItemByUrl($language['id'], $url.$i);
          $category = \Modules\catalog\categories\Db::getCategoryByUrl($language['id'], $url.$i);
          while($item && $item['record_id'] != $rowId || $category){
            $i++;
          }
          $_REQUEST[$prefix.'_'.$language['id']] = $url.$i;     
        }       
      }            
    
    }
    
    parent::processUpdate($prefix, $rowId, $area);
  }    
  
    
  
}