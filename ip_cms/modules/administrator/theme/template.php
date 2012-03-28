<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2012 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\administrator\theme;

if (!defined('CMS')) exit;

global $parametersMod;

class Template{

    public static function header(){
        return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>ImpressPages</title>
        <link rel="stylesheet" href="'.BASE_URL.BACKEND_DIR.'design/ip_admin.css">
        <link rel="stylesheet" href="'.BASE_URL.MODULE_DIR.'administrator/system/style.css">
        <script src="'.LIBRARY_DIR.'js/default.js"></script>
        <script src="'.LIBRARY_DIR.'js/jquery/jquery.js"></script>
        <script src="'.MODULE_DIR.'administrator/theme/public/ipTheme.js"></script>
        </head>
    <body>
          ';
    }
    
    
    public static function title() {
        global $parametersMod;
        $answer = '<h1>'.htmlspecialchars($parametersMod->getValue('administrator', 'theme', 'admin_translations', 'title')).'</h1>';
        return $answer;
    }
    
    public static function themes($themes) {
        global $parametersMod;
        global $cms;
        $answer = '';

        $answer .= '<ul>';
        foreach($themes as $theme) {
            
            if ($theme->getPreviewImage()) {
                $image = '<img width="800" src="'.BASE_URL.$theme->getPreviewImage().'" alt="'.htmlspecialchars($theme->getName()).'"/>';
            } else {
                $image = '';
            }
            
            $answer .= '
<li>
    '.htmlspecialchars($theme->getName()).'
    '.$image.'
    <a class="installTheme" data-themename="'.htmlspecialchars($theme->getName()).'" href="'.$cms->generateUrl($cms->curModId, 'action=changeTheme&themeName='.$theme->getName()).'">'.htmlspecialchars($parametersMod->getValue('administrator', 'theme', 'admin_translations', 'install')).'</a>
</li>
            ';
        }
        $answer .= '</ul>';
        
        return $answer;
    }
    
    
    public static function message($message) {
        $answer = '<span class="note">'.htmlspecialchars($message).'</span>';
        return $answer;
    }

    public static function error($error) {
        $answer = '<span class="note">'.htmlspecialchars($error).'</span>';
        return $answer;
    }
    
    public static function footer(){
        return "</body></html>";
    }

}

