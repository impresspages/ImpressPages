<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace Modules\catalog\items;

if (!defined('CMS')) exit;

class Install{

  public function execute(){
    $sql="
    CREATE TABLE IF NOT EXISTS `".DB_PREF."m_catalog_item` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `category_id` int(11) DEFAULT NULL,
      `priority` int(11) NOT NULL DEFAULT '0',
      `price` decimal(10,2) DEFAULT NULL,
      `discount` decimal(10,2) DEFAULT NULL,
      `quantity` int(11) NOT NULL,
      `file` varchar(255) DEFAULT NULL,
      `first_photo` varchar(255) DEFAULT NULL,
      `first_photo_big` varchar(255) DEFAULT NULL,
      `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',      
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;
    ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    
    
    $sql="
    CREATE TABLE IF NOT EXISTS `".DB_PREF."m_catalog_item_photo` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `record_id` int(11) NOT NULL,
      `row_number` int(11) NOT NULL,
      `photo` varchar(255) NOT NULL,
      `photo_big` varchar(255) NOT NULL,
      `title` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=121 ;
    ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    
    
    $sql="
    CREATE TABLE IF NOT EXISTS `".DB_PREF."m_catalog_item_translation` (
      `translation_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '\"translation\" to not collide with item id',
      `record_id` int(11) NOT NULL,
      `language_id` int(11) NOT NULL,
      `visible` tinyint(1) NOT NULL,
      `title` varchar(255) DEFAULT NULL,
      `description` mediumtext,
      `meta_keywords` text,
      `meta_description` text,
      `meta_url` varchar(255) DEFAULT NULL,
      `text` mediumtext COMMENT 'text version of page content for search',
      `html` mediumtext COMMENT 'html version of page content for search',      
      PRIMARY KEY (`translation_id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=138 ;
    ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    
    
      
    $sql = "ALTER TABLE `".DB_PREF."m_catalog_item` ADD INDEX ( `category_id` ) ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    
    $sql = "ALTER TABLE `".DB_PREF."m_catalog_item_photo` ADD INDEX ( `record_id` ) ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    $sql = "ALTER TABLE `".DB_PREF."m_catalog_item_translation` ADD INDEX ( `record_id` , `language_id` ) ;";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    $sql = "ALTER TABLE `".DB_PREF."m_catalog_item_translation` ADD INDEX ( `meta_url` )   ";
    
    $rs = mysql_query($sql);
    
    if(!$rs){ 
      trigger_error($sql." ".mysql_error());
    }
    
    
  }
}
