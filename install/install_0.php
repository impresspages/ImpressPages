<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
if (!defined('INSTALL')) exit;

complete_step(0);


$answer = '<h1>'.IP_STEP_LANGUAGE_LONG."</h1>";

$selected_language = (isset($_SESSION['installation_language']) ? $_SESSION['installation_language'] : 'en');

$languages = array();
$languages['cs'] = 'Čeština';
$languages['nl'] = 'Dutch';
$languages['en'] = 'English';
$languages['fr'] = 'French';
$languages['de'] = 'Deutsch';
$languages['ja'] = '日本語';
$languages['lt'] = 'Lietuvių';
$languages['pt'] = 'Portugues';
$languages['pl'] = 'Polski';
$languages['ro'] = 'Română';

foreach($languages as $key => $language){
    if ($key == $selected_language) {
        $answer .= '<a class="selected" href="index.php?step=2&lang='.htmlspecialchars($key).'">'.htmlspecialchars($language).'</a><br/>';
    } else {
        $answer .= '<a href="index.php?step=2&lang='.htmlspecialchars($key).'">'.htmlspecialchars($language).'</a><br/>';
    }
}

$answer .= '<br/><br/>';

output($answer.'<a class="button_act" href="?step=2">'.IP_NEXT.'</a>'
);


?>
