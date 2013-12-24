<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid\Model;


class Status
{

    public static function parse($statusVariable) {
        if (!empty($statusVariable[0]) && $statusVariable[0] == '#') {
            $statusVariable = substr($statusVariable, 1);
        }

        $variables = array();
        $parts = explode('&', $statusVariable);
        foreach($parts as $part) {
            $tmp = explode('=', $part);
            if (isset($tmp[1])) {
                $variables[$tmp[0]] = $tmp[1];
            } else {
                $variables[$tmp[0]] = null;
            }
        }
        return $variables;
    }

    public static function build($variables)
    {
        return 'grid&' . http_build_query($variables);
    }

//    protected $config = null;
//
//    public function __construct($hash)
//    {
//        $this->config = fixMagicQuotes(parse_str($hash));
//    }
//
//
//    public function hash()
//    {
//
//    }
//
//    protected function fixMagicQuotes($data)
//    {
//        if (!get_magic_quotes_gpc()) {
//            return;
//        }
//
//        $process = array(&$data);
//        while (list($key, $val) = each($process)) {
//            foreach ($val as $k => $v) {
//                unset($process[$key][$k]);
//                if (is_array($v)) {
//                    $process[$key][stripslashes($k)] = $v;
//                    $process[] = & $process[$key][stripslashes($k)];
//                } else {
//                    $process[$key][stripslashes($k)] = stripslashes($v);
//                }
//            }
//        }
//        unset($process);
//    }
}