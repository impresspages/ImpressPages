<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace Modules\shop\currencies_to_languages;

if (!defined('CMS')) exit;

class Uninstall{

  public function execute(){
    
    $sql="
    DROP TABLE IF EXISTS `".DB_PREF."m_shop_currency_to_language`;
    ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
  }
}

