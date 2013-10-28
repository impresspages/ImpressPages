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
    <meta charset="'.CHARSET.'">
    <title>ImpressPages</title>
    <link rel="stylesheet" href="'.BASE_URL.CORE_DIR.'Ip/Backend/design/ip_admin.css">
    <link rel="stylesheet" href="'.BASE_URL.MODULE_DIR.'developer/config_exp_imp/design/style.css">
    <script src="' . \Ip\Config::libraryUrl('js/default.js') . '"></script>
</head>
<body>
      ';
    }


    public static function footer(){
        return "</body></html>";
    }
}


