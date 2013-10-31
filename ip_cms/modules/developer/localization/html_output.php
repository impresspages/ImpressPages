<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\developer\localization;


require_once (__DIR__.'/html_output.php');


class HtmlOutput{
    public static function header(){
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="'.\Ip\Config::getRaw('CHARSET').'">
    <title>ImpressPages</title>
    <link rel="stylesheet" href="' . \Ip\Config::coreModuleUrl('Admin/assets/backend/ip_admin.css') . '">
    <link rel="stylesheet" href="' . \Ip\Config::oldModuleUrl('developer/config_exp_imp/design/style.css') . '">
    <script src="' . \Ip\Config::libraryUrl('js/default.js') . '"></script>
</head>
<body>
      ';
    }


    public static function footer(){
        return "</body></html>";
    }
}


