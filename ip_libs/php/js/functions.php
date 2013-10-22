<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace Library\Php\Js;

/**
 * replaces special characters in a string
 * @package Library
 */
class Functions
{
    public static function htmlToString($html) {
        return str_replace('script',"scr' + 'ipt", str_replace("\r", "", str_replace("\n", "\\n' + \n '", str_replace("'", "\\'",str_replace("\\", "\\\\",$html)))));
    }
}