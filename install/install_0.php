<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
if (INSTALL!="true") exit;
 
complete_step(0); 


$answer = '<h1>'.IP_STEP_LANGUAGE_LONG."</h1>";  



$languages = array();
$languages['nl'] = 'Dutch';
$languages['en'] = 'English';
$languages['lt'] = 'Lietuvių';
$languages['pl'] = 'Polski';
$languages['ro'] = 'Română';

foreach($languages as $key => $language){
  $answer .= '<a href="index.php?lang='.htmlspecialchars($key).'">'.htmlspecialchars($language).'</a><br/>';
}

$answer .= '<br/><br/>';

output($answer.'<a class="button_act" href="?step=2">'.IP_NEXT.'</a>'
);


?>	