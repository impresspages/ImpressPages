<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

namespace Modules\community\newsletter_subscribers;

if (!defined('BACKEND')) exit;  
class element_newsletter_email extends \Library\Php\StandardModule\Element_text{ //data element in area
  var $default_value;
  var $mem_value;
  var $reg_expression;
  var $max_length;
	

  function check_field($prefix, $action){
    global $parametersMod;
    $sql = "select * from `".DB_PREF."m_community_newsletter_subscribers` where `email` = '".mysql_real_escape_string($_POST[$prefix])."' and `language_id` = '".mysql_real_escape_string($_GET['road'][0])."' and `verified` ";
    global $log;
    $log->log("sql", $sql);
    $rs = mysql_query($sql);
    if ($rs) {
      if (mysql_num_rows($rs) > 0) {
        return $parametersMod->getValue('community', 'newsletter_subscribers', 'admin_translations', 'error_registered');
      } else {
        return null;
      }
    } else {
      trigger_error($sql." ".mysql_error());
    }
  }



}
