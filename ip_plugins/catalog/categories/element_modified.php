<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see license.html
 */
namespace Modules\catalog\categories;

if (!defined('BACKEND')) exit;

require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/std_mod.php');

class ElementModified extends \Modules\developer\std_mod\ElementDateTime{
  

  
  function printFieldUpdate($prefix, $record, $area){
    $value = null;

    $value = $record[$this->dbField];

    $html = new \Modules\developer\std_mod\StdModHtmlOutput();
    $html->dateTime($prefix, date("Y-m-d H:i:s"), $this->disabledOnUpdate);
    return $html->html;
  }
    
  
}