<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
 
namespace Modules\standard\breadcrumb; 
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

  
/**
 * class to ouput current breadcrumb
 * @package ImpressPages
 */   
class Module{
  
  /**
   * @return string HTML with links to website languages
   */     
  static function generateBreadcrumb($separator){ 
    global $site;
    global $parametersMod;
		
    $breadCrumb = $site->getBreadcrumb();
		
			
    $site->requireTemplate('standard/breadcrumb/template.php');
    return Template::breadcrumb($breadCrumb, $separator);
  }
}