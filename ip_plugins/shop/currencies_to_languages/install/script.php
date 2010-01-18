<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace Modules\shop\currencies_to_languages;

if (!defined('CMS')) exit;

class Install{

  public function execute(){

    $sql="
    CREATE TABLE IF NOT EXISTS `".DB_PREF."m_shop_currency_to_language` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `language_id` int(11) NOT NULL,
      `currency_id` int(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;
    ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    
    
    
    $sql = "ALTER TABLE `".DB_PREF."m_shop_currency_to_language` ADD INDEX ( `language_id` , `currency_id` ) ;";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
  }
}