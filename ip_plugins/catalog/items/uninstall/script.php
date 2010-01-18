<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace Modules\catalog\items;

if (!defined('CMS')) exit;

class Uninstall{

  public function execute(){

    $sql="
    DROP TABLE IF EXISTS `".DB_PREF."m_catalog_item`;
    ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    
    
    $sql="
    DROP TABLE IF EXISTS `".DB_PREF."m_catalog_item_photo`;
    ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    
    
    
    
    
    $sql="
    DROP TABLE IF EXISTS `".DB_PREF."m_catalog_item_translation`;
    ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }

  }
}
