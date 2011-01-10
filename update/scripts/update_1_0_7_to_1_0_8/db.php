<?php

/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace update_1_0_7_to_1_0_8;



if (!defined('CMS')) exit;

class Db {


  public static function updateContactFormWidget() {
    $sql = "
      ALTER TABLE `".DB_PREF."mc_misc_contact_form_field` ADD `values` TEXT NULL COMMENT 'json array'
    ";

    $rs = mysql_query($sql);
    if($rs) {
      return true;
    } else {
      //trigger_error($sql.' '.mysql_error());
      return false;
    }
  }


  public static function createTableWidgetTables() {
    $sql = "
      CREATE TABLE IF NOT EXISTS `".DB_PREF."mc_text_photos_table` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `text` mediumtext NOT NULL,
        `layout` varchar(255) NOT NULL DEFAULT 'default',
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;
    ";

    $rs = mysql_query($sql);
    if($rs) {
      return true;
    } else {
      trigger_error($sql.' '.mysql_error());
    }
  }

  public static function addWidget($groupId, $data) {

    if( !isset($data['name']) ) {
      trigger_error("No widget name specified.");
      return false;
    }

    if( !isset($data['group_id']) ) {
      trigger_error("No group_id specified.");
      return false;
    }


    if ( self::getWidgetByGroupAndName($data['group_id'], $data['name']) ) {
      //trigger_error("Widget already exists.");
      return false;
    }

    $sql = "insert into `".DB_PREF."content_module` set ";
    $first = true;
    foreach ($data as $dataKey => $dataVal) {
      if (!$first) {
        $sql .= ', ';
      }
      $sql .= " `".$dataKey."` = '".mysql_real_escape_string($dataVal)."' ";
      $first = false;
    }

    $rs = mysql_query($sql);
    if($rs) {
      return mysql_insert_id();
    } else {
      trigger_error($sql.' '.mysql_error());
    }
  }
  
  public static function getWidgetByGroupAndName($groupId, $widgetName) {
    $sql = "select * from `".DB_PREF."content_module` where `name` = '".mysql_real_escape_string($widgetName)."' and `group_id` = '".(int)$groupId."' ";
    $rs = mysql_query($sql);
    if($rs) {
      if ($lock = mysql_fetch_assoc($rs)) {
        return $lock;
      } else {
        return false;
      }
    } else {
      trigger_error($sql.' '.mysql_error());
    }
  }

  public static function getMaxWidgetGroupRow($groupId) {
    $sql = "select max(row_number) as 'max' from `".DB_PREF."content_module` where `group_id` = '".(int)$groupId."' ";
    $rs = mysql_query($sql);
    if($rs) {
      if ($lock = mysql_fetch_assoc($rs)) {
        return $lock['max'];
      } else {
        return false;
      }
    } else {
      trigger_error($sql.' '.mysql_error());
    }
  }


  public static function getWidgetGroup($groupName) {
    $sql = "select * from `".DB_PREF."content_module_group` where `name` = '".mysql_real_escape_string($groupName)."' ";
    $rs = mysql_query($sql);
    if($rs) {
      if ($lock = mysql_fetch_assoc($rs)) {
        return $lock;
      } else {
        return false;
      }
    } else {
      trigger_error($sql.' '.mysql_error());
    }
  }

}