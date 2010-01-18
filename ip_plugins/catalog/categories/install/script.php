<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace Modules\catalog\categories;

if (!defined('CMS')) exit;

class Install{

  public function execute(){

    $sql="
    CREATE TABLE IF NOT EXISTS `".DB_PREF."m_catalog_category` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `parent_id` int(11) DEFAULT NULL,
      `priority` int(11) NOT NULL DEFAULT '0',
      `photo` varchar(255) DEFAULT NULL,
      `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `modified` timestamp NULL DEFAULT NULL,      
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=109 ;
    ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    
    
    $sql="
    CREATE TABLE IF NOT EXISTS `".DB_PREF."m_catalog_category_translation` (
      `translation_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '\"translation\" to not collide with category id',
      `record_id` int(11) NOT NULL,
      `language_id` int(11) NOT NULL,
      `visible` tinyint(1) NOT NULL,
      `button_title` text NOT NULL,
      `page_title` text NOT NULL,
      `keywords` text NOT NULL,
      `description` text NOT NULL,
      `url` varchar(255) NOT NULL,
      PRIMARY KEY (`translation_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=142 ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    
    
    $sql = "ALTER TABLE `".DB_PREF."m_catalog_category` ADD INDEX ( `parent_id` )";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    
    $sql = "ALTER TABLE `".DB_PREF."m_catalog_category_translation` ADD INDEX ( `record_id` , `language_id` ) ;";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    
    $sql = "ALTER TABLE `".DB_PREF."m_catalog_category_translation` ADD INDEX ( `url` ) ; ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }

  }
} 
  
