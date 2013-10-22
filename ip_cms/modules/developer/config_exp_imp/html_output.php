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
    <link rel="stylesheet" href="'.BASE_URL.BACKEND_DIR.'design/ip_admin.css">
    <link rel="stylesheet" href="'.BASE_URL.MODULE_DIR.'developer/config_exp_imp/design/style.css">
    <script src="'.BASE_URL.LIBRARY_DIR.'js/default.js"></script>
</head>
<body>
      ';
    }


    public static function footer(){
        return "</body></html>";
    }
}


