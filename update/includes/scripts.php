<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */


if (!defined('CMS')) exit;


class Scripts {
  private $scripts;
  const destinationVersion = '1.0.9rc3';
  
  public function __construct(){
    $this->scripts = array();        
    $this->scripts[] = array("from" => "1.0.0 Alpha", "to" => "1.0.1 Beta", "script" => "update_1_0_0_alpha_to_1_0_1_beta");
    $this->scripts[] = array("from" => "1.0.1 Beta", "to" => "1.0.2 Beta", "script" => "update_1_0_1_beta_to_1_0_2_beta");
    $this->scripts[] = array("from" => "1.0.2 Beta", "to" => "1.0.3 Beta", "script" => "update_1_0_2_beta_to_1_0_3_beta");
    $this->scripts[] = array("from" => "1.0.3 Beta", "to" => "1.0.4", "script" => "update_1_0_3_beta_to_1_0_4");
    $this->scripts[] = array("from" => "1.0.4", "to" => "1.0.5", "script" => "update_1_0_4_to_1_0_5");
    $this->scripts[] = array("from" => "1.0.5", "to" => "1.0.6", "script" => "update_1_0_5_to_1_0_6");
    $this->scripts[] = array("from" => "1.0.6", "to" => "1.0.7", "script" => "update_1_0_6_to_1_0_7");
    $this->scripts[] = array("from" => "1.0.7", "to" => "1.0.8", "script" => "update_1_0_7_to_1_0_8");
    
    $this->scripts[] = array("from" => "1.0.8", "to" => "1.0.9", "script" => "update_1_0_8_to_1_0_9");
    $this->scripts[] = array("from" => "1.0.9rc2", "to" => "1.0.9", "script" => "update_1_0_8_to_1_0_9");
    $this->scripts[] = array("from" => "1.0.9rc3", "to" => "1.0.9", "script" => "update_1_0_8_to_1_0_9");
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





