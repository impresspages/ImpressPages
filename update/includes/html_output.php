<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */


if (!defined('CMS')) exit;

class HtmlOutput {

  public function header () {
    return 
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>'.IP_INSTALLATION.'</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="robots" content="NOINDEX,NOFOLLOW">
  <link href="design/style.css" rel="stylesheet" type="text/css" />  
  <link rel="SHORTCUT ICON" href="favicon.ico" />
</head>   
<body>

  <div class="container">
    <img id="logo" src="design/cms_logo.gif" alt="ImpressPages CMS" />
    <div class="clear"></div>
    <div id="wrapper">
      <p id="installationNotice">'.IP_INSTALLATION.'</span></p>
      <div class="clear"></div>
      <img class="border" src="design/cms_main_top.gif" alt="Design" />
      <div id="main">
        <div id="menu">
        '.HtmlOutput::generateMenu().'
        </div>
        <div id="content">    
';
  }
  
  public function footer () {
    return 
'       </div>
        <div class="clear"></div>
      </div>
      <img class="border" src="design/cms_main_bottom.gif" alt="Design" />
      <div class="clear"></div>
    </div>
    <div class="footer">Copyright 2007-'.date("Y").' by <a href="http://www.impresspages.org">ImpressPages LTD</a></div>
  </div>

	<script type="text/javascript">
	<!--
	if (document.images)
	{
		preload_image = new Image(); 
		preload_image.src="design/cms_button_hover.gif"; 
	}
	//-->
	</script>
  	
</body>';

  }
  
  
  private function generateMenu(){
    global $navigation;
    
    $curStep = $navigation->curStep();
    
  	$steps = array();
  	$steps[] = IP_STEP_BACKUP;
  	$steps[] = IP_STEP_PROCESS;
  	$steps[] = IP_STEP_FINISH;
  
  	$answer = '
  	<ul>	
  	';
  
  	foreach($steps as $key => $step){
  		$class = "";
  		if($curStep >= $key+1)
  			$class="completed";
  		else
  			$class="incompleted";			
  		if($key+1 == $curStep)
  			$class="current";
  		if($key == $curStep)
  			$answer .= '<li class="'.$class.'"><a>'.$step.'</a></li>';
  		else
  			$answer .= '<li class="'.$class.'"><a>'.$step.'</a></li>';
  		
  	}
  	
  	$answer .= '
  	</ul>
  	';
	
  	return $answer;
  }	  
  
  public function h1 ($title) {
    return '<h1>'.htmlspecialchars($title).'</h1>';
  }
  
  function table ($table){
  	$answer = '';
  	
  	$answer .= '<table>';
  	$i = 0;
  	while(sizeof($table) > ($i + 1)){
  		$answer .= '<tr><td class="label">'.$table[$i].'</td><td class="value">'.$table[$i+1].'</td></tr>';
  		$i += 2;
  	}
  	
  	$answer .= '</table>';
  	return $answer;
  }	
 
  function button($title, $link = null, $action = null) {
    return '<a class="button_act" href="'.$link.'" onclick="'.$action.'">'.htmlspecialchars($title).'</a>';
  }
	
  
}





