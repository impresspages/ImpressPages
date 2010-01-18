<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace Modules\shop\currencies;

if (!defined('CMS')) exit;

class Install{

  public function execute(){

    $sql="
    CREATE TABLE IF NOT EXISTS `".DB_PREF."m_shop_currency` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `row_number` int(11) NOT NULL,
      `title` varchar(255) NOT NULL,
      `code` char(3) NOT NULL,
      `rate` double NOT NULL,
      `default` tinyint(1) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;
    ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    $sql = "
    SELECT * FROM `".DB_PREF."m_shop_currency` WHERE 1
    ";
    
    $rs = mysql_query($sql);
    if($rs){ 
      if(mysql_num_rows($rs) == 0){//if no records exist
        $sql = "
        INSERT INTO `".DB_PREF."m_shop_currency` (`id`, `row_number`, `title`, `code`, `rate`, `default`) VALUES
        (20, 0, 'Euro', 'EUR', 1, 1),
        (21, 1, 'Dollar', 'USD', 1.4313, 0),
        (22, 2, 'Great Britany Pounds', 'GBP', 0.899566338, 0);
        
        ";
        
        $rs = mysql_query($sql);
        
        if(!$rs){ 
          trigger_error($sql." ".mysql_error());
        }
      }
    } else {
      trigger_error($sql." ".mysql_error());
    }
    
    
    
    $sql = "ALTER TABLE `".DB_PREF."m_shop_currency` ADD INDEX ( `row_number` )";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    $sql = "ALTER TABLE `".DB_PREF."m_shop_currency` ADD INDEX ( `code` ) ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    $sql = "ALTER TABLE `".DB_PREF."m_shop_currency` ADD INDEX ( `default` ) ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }

  }
}
