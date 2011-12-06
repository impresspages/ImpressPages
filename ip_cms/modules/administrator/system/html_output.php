<?php

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
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
    <link rel="stylesheet" href="'.BASE_URL.BACKEND_DIR.'design/common.css">
    <link rel="stylesheet" href="'.BASE_URL.MODULE_DIR.'administrator/system/style.css">
    <script src="'.LIBRARY_DIR.'js/default.js"></script>
</head>
<body>
      ';
    }


    public static function footer(){
        return "</body></html>";
    }
}


