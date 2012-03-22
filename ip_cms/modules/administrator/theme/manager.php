<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2012 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\theme;

if (!defined('BACKEND')) exit;

require_once(__DIR__.'/template.php');
require_once(__DIR__.'/model.php');

class Manager{
    function __construct(){

    }
    function manage(){
        global $parametersMod;
        $error = '';
        $message = '';
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'changeTheme' && isset($_REQUEST['themeName'])) {
            try {
                Model::installTheme($_REQUEST['themeName']);
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
            
            if (!$error) {
                $message = $parametersMod->getValue('administrator', 'theme', 'admin_translations', 'successful_install');
            }
        }
        
        
        $themes = Model::getAvailableThemes();
        
        $answer = Template::header();
        $answer.= Template::title();
        if ($error) {
            Template::error($error);
        }
        if ($message) {
            Template::message($message);
        }
        $answer.= Template::themes($themes);
        $answer.= Template::footer();
        
        return $answer;
         
    }


}
