<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace Modules\shop\currencies;

if (!defined('CMS')) exit;

class Uninstall{

  public function execute(){

    $sql="
    DROP TABLE IF EXISTS `".DB_PREF."m_shop_currency`;
    ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }

  }
}
