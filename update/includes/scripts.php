<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */


if (!defined('CMS')) exit;


class Scripts {
  private $scripts;
  const destinationVersion = '1.0.4';
  
  public function __construct(){
    $this->scripts = array();        
    $this->scripts[] = array("from" => "1.0.0 Alpha", "to" => "1.0.1 Beta", "script" => "update_1_0_0_alpha_to_1_0_1_beta");
    $this->scripts[] = array("from" => "1.0.1 Beta", "to" => "1.0.2 Beta", "script" => "update_1_0_1_beta_to_1_0_2_beta");
    $this->scripts[] = array("from" => "1.0.2 Beta", "to" => "1.0.3 Beta", "script" => "update_1_0_2_beta_to_1_0_3_beta");
    $this->scripts[] = array("from" => "1.0.3 Beta", "to" => "1.0.4", "script" => "update_1_0_3_beta_to_1_0_4");
  }

  public function getScripts($fromVersion = "1.0.0 Alpha"){
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





