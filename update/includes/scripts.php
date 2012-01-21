<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2012 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */


if (!defined('CMS')) exit;


class Scripts {
  private $scripts;
  const destinationVersion = '2.0rc2';
  
  public function __construct(){
    $this->scripts = array();
    $this->scripts[] = array("from" => "2.0rc1", "to" => "2.0rc2", "script" => "update_2_0_rc1_to_2_0_rc2");
  }

  public function getScripts($fromVersion = "2.0rc1"){
    $answer = array();
    
    $currentScript = false;
    while($currentScript = $this->getScript($fromVersion)){
      $answer[] = $currentScript;
      $fromVersion = $currentScript['to'];
    }
    
    return $answer;
  }

  public function getScript($fromVersion){
    $answer = false;
    
    foreach ($this->scripts as $script) {
      if ($script['from'] == $fromVersion){
        $answer = $script;
      }
    } 
    
    return $answer;
  }
  
  public function nextVersin($version){
  
  }
  
  public function updateClass($fromVersion, $toVersion){
  
  }
  
    
}





