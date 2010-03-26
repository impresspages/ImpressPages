<?php

/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */


namespace Modules\administrator\system; 

if (!defined('BACKEND')) exit;  

require_once (__DIR__.'/html_output.php');
require_once (__DIR__.'/module.php');
require_once (BASE_DIR.INCLUDE_DIR.'db_system.php');

class Manager{
  function manage(){
    global $cms;
    global $parametersMod;
		global $log;
		
    $answer = '';
    if(isset($_GET['action'])){
      switch($_GET['action']){
        case "cache_clear":
          $log->log('administrator/system', 'Cache was cleared');
          
          $module = new Module;
          $module->clearCache();
      			$answer .= '
      <div class="note">
        '.$parametersMod->getValue('administrator', 'system', 'admin_translations', 'cache_cleared').'
      </div>
            ';      			
      		      		
        break;
      }
    }    
    
    
    $answer .= HtmlOutput::header();
    $answer .= '<div class="content">'; 
    $answer .= '<h1>ImpressPages CMS '.htmlspecialchars(\DbSystem::getSystemVariable('version')).'</h1>';
    $answer .= '</div>';     
    $answer .= '<div class="content">'; 
    $answer .= '<h1>'.htmlspecialchars($parametersMod->getValue('administrator', 'system', 'admin_translations', 'cache')).'</h1>';
    $answer .= $parametersMod->getValue('administrator', 'system', 'admin_translations', 'cache_comments');            
    $answer .= '<a href="'.$cms->generateUrl($cms->curModId, 'action=cache_clear').'" class="button">'.htmlspecialchars($parametersMod->getValue('administrator', 'system', 'admin_translations', 'cache_clear')).'</a><br/><br/><br/>';            
    $answer .= '</div>';     
    $answer .= HtmlOutput::footer();
    
    return $answer;
  }
}

