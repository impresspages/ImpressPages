<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see ip_license.html
 */


if (!defined('CMS')) exit;


class Navigation {

  public function curStep () {
    if(isset($_GET['step']))
      $step = (int)$_GET['step'];
    else
      $step = 1;
      
    return $step; 
  }
  
  public function curScript() {
    if(isset($_GET['script']))
      $step = (int)$_GET['script'];
    else
      $step = 1;
      
    return $step; 
  }
  
  public function curAction() {
    if(isset($_GET['action']))
      $action = (int)$_GET['action'];
    else
      $action = 1;
      
    return $action; 
  }

  public function generateLink($step, $script = null, $action = null) {
    if ($script === null) {
      return "index.php?step=".$step;
    } else {
      if ($action === null)
        return "index.php?step=".$step."&script=".$script;
      else
        return "index.php?step=".$step."&script=".$script."&action=".$action;
    }
    
  }
}




