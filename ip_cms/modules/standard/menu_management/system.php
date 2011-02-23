<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\standard\menu_management;  

if (!defined('CMS')) exit;


class System {

  function clearCache($cachedBaseUrl) {

    $sql = "update `".DB_PREF."content_element` set `redirect_url` = REPLACE(`redirect_url`, '".mysql_real_escape_string($cachedBaseUrl)."', '".mysql_real_escape_string(BASE_URL)."')  where 1 ";
    $rs = mysql_query($sql);
    if (!$rs) {
      trigger_error($sql." ".mysql_error());
    }
    
  }
}