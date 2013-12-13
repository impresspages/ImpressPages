<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid1;


class Status
{

    protected $config = null;

    public function __construct($hash)
    {
        $this->config = fixMagicQuotes(parse_str($hash));
    }


    public function hash()
    {

    }

    protected function fixMagicQuotes($data)
    {
        if (!get_magic_quotes_gpc()) {
            return;
        }

        $process = array(&$data);
        while (list($key, $val) = each($process)) {
            foreach ($val as $k => $v) {
                unset($process[$key][$k]);
                if (is_array($v)) {
                    $process[$key][stripslashes($k)] = $v;
                    $process[] = & $process[$key][stripslashes($k)];
                } else {
                    $process[$key][stripslashes($k)] = stripslashes($v);
                }
            }
        }
        unset($process);
    }
}