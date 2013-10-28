<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\developer\config_exp_imp;


require_once (__DIR__.'/html_output.php');


class HtmlOutput{
    public static function header(){
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="'.CHARSET.'">
    <title>ImpressPages</title>
    <link rel="stylesheet" href="' . \Ip\Config::coreUrl('Ip/Backend/design/ip_admin.css') . '">
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


