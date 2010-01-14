<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
 
namespace Modules\standard\languages; 
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

/** @private */
require_once (__DIR__.'/db.php');
  
/**
 * class to ouput the languages
 * @package ImpressPages
 */   
class Module{
  
  /**
   * @return string HTML with links to website languages
   */     
  public static function generatehtml(){ 
    global $site;
    global $parametersMod;
		
		if(!$parametersMod->getValue('standard', 'languages', 'options', 'multilingual'))
			return;
			
    $site->requireTemplate('standard/languages/template.php');
    return Template::languages($site->languages);
  }
}