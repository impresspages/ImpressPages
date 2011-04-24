<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\menu_management;


if (!defined('BACKEND')) exit;



class Manager {

  function __construct() {
  }


  function manage() {
    global $cms;
    
    require_once (__DIR__.'/template.php');
    
    $content = 'Test';
    
    $data = array (
      'securityToken' =>  $cms->session->securityToken(),
      'moduleId' => $cms->curModId,
      'postURL' => $cms->generateWorkerUrl()
    );
    
    $content = Template::content($data);
    

    
    $answer = Template::addLayout($content);
    
    return $answer;
  }
  
}
