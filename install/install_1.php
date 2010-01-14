<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see license.html
 */
if (INSTALL!="true") exit;
 
complete_step(1); 
 
function directory_is_writeable($dir){
	$answer = true;
	if(!is_writable($dir)){
		$answer = false;
	}
		
	if ($handle = opendir($dir)) { 
	    while (false !== ($file = readdir($handle))) {
				if($file != ".." && !is_writable($dir.'/'.$file)){
					$answer = false;				
				}
	    }
	    closedir($handle);
	} 

	return $answer;
}

$error = array();

if(PHP_MAJOR_VERSION < 5)
	$error['php_version'] = 1;
if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 3)
	$error['php_version'] = 1;
  	
if(function_exists('apache_get_modules') ){
 if(!in_array('mod_rewrite',apache_get_modules()))
  $error['mod_rewrite'] = 1;
}	
	
$answer = '';	
$answer = '<h1>'.IP_STEP_CHECK_LONG."</h1>";	
	
$table = array();

$table[] = IP_PHP_VERSION;
if(isset($error['php_version']))
	$table[] = '<span class="error">'.IP_ERROR."</span>";
else
	$table[] = '<span class="correct">'.IP_OK.'</span>';	

$table[] = IP_MOD_REWRITE;
if(isset($error['mod_rewrite']))
	$table[] = '<span class="error">'.IP_ERROR."</span>";
else
	$table[] = '<span class="correct">'.IP_OK.'</span>';	


$table[] = '';
$table[] = '';


$table[] = '';
$table[] = '';


$table[] = '<b>/audio/</b> '.IP_WRITEABLE.' '.IP_SUBDIRECTORIES;
if(!directory_is_writeable(dirname(__FILE__).'/../audio')){
	$table[] = '<span class="error">'.IP_ERROR."</span>";
	$error['writeable_audio'] = 1;
}else
	$table[] = '<span class="correct">'.IP_OK.'</span>';


$table[] = '<b>/file/</b> '.IP_WRITEABLE.' '.IP_SUBDIRECTORIES;
if(!directory_is_writeable(dirname(__FILE__).'/../file')){
	$table[] = '<span class="error">'.IP_ERROR."</span>";
	$error['writeable_file'] = 1;
}else
	$table[] = '<span class="correct">'.IP_OK.'</span>';	
	

$table[] = '<b>/image/</b> '.IP_WRITEABLE.' '.IP_SUBDIRECTORIES;
if(!directory_is_writeable(dirname(__FILE__).'/../image')){
	$table[] = '<span class="error">'.IP_ERROR."</span>";
	$error['writeable_image'] = 1;
}else
	$table[] = '<span class="correct">'.IP_OK.'</span>';	

$table[] = '<b>/video/</b> '.IP_WRITEABLE.' '.IP_SUBDIRECTORIES;
if(!directory_is_writeable(dirname(__FILE__).'/../video')){
	$table[] = '<span class="error">'.IP_ERROR."</span>";
	$error['writeable_video'] = 1;
}else
	$table[] = '<span class="correct">'.IP_OK.'</span>';	


$table[] = '<b>/ip_config.php</b> '.IP_WRITEABLE;
if(!is_writeable(dirname(__FILE__).'/../ip_config.php')){
	$table[] = '<span class="error">'.IP_ERROR."</span>";
	$error['writeable_config'] = 1;
}else
	$table[] = '<span class="correct">'.IP_OK.'</span>';


$table[] = '<b>/robots.txt</b> '.IP_WRITEABLE;
if(!is_writeable(dirname(__FILE__).'/../robots.txt')){
	$table[] = '<span class="error">'.IP_ERROR."</span>";
	$error['writeable_robots'] = 1;
}else
	$table[] = '<span class="correct">'.IP_OK.'</span>';	



/*$table[] = '<b>/library/js/tiny_mce/plugins/simplebrowser/assets/file</b> '.IP_WRITEABLE;
if(!directory_is_writeable(dirname(__FILE__).'/../library/js/tiny_mce/plugins/simplebrowser/assets/file')){
	$table[] = '<span class="error">'.IP_ERROR."</span>";
}else
	$table[] = '<span class="correct">'.IP_OK.'</span>';	

	$table[] = '<b>/library/js/tiny_mce/plugins/simplebrowser/assets/flash</b> '.IP_WRITEABLE;
if(!directory_is_writeable(dirname(__FILE__).'/../library/js/tiny_mce/plugins/simplebrowser/assets/flash')){
	$table[] = '<span class="error">'.IP_ERROR."</span>";
}else
	$table[] = '<span class="correct">'.IP_OK.'</span>';	

	
$table[] = '<b>/library/js/tiny_mce/plugins/simplebrowser/assets/image</b> '.IP_WRITEABLE;
if(!directory_is_writeable(dirname(__FILE__).'/../library/js/tiny_mce/plugins/simplebrowser/assets/image')){
	$table[] = '<span class="error">'.IP_ERROR."</span>";
}else
	$table[] = '<span class="correct">'.IP_OK.'</span>';	

	$table[] = '<b>/library/js/tiny_mce/plugins/simplebrowser/assets/media</b> '.IP_WRITEABLE;
if(!directory_is_writeable(dirname(__FILE__).'/../library/js/tiny_mce/plugins/simplebrowser/assets/media')){
	$table[] = '<span class="error">'.IP_ERROR."</span>";
}else
	$table[] = '<span class="correct">'.IP_OK.'</span>';	
*/

	
$answer .= gen_table($table);

$answer .= '<br><br>';
if(sizeof($error) > 0){  
	$_SESSION['step'] = 1;
	$answer .= '<a class="button_act" href="?step=1">'.IP_CHECK_AGAIN.'</a>';
}else{
	complete_step(1);
	$answer .= '<a class="button_act" href="?step=2">'.IP_NEXT.'</a><a class="button" href="?step=1">'.IP_CHECK_AGAIN.'</a>';
}
$answer .= "<br>";

output($answer);


?>	