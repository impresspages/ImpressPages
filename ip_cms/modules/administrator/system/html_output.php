<?php

/**
 * @package ImpressPages
 *
 *
 */


namespace Modules\administrator\system;
if (!defined('BACKEND')) exit;

require_once (__DIR__.'/html_output.php');


class HtmlOutput{
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
</head>
<body>
    <script>
    var BASE_URL = \''.BASE_URL.'\';
    </script>
      ';
    }


    public static function footer(){
        return "</body></html>";
    }
}


