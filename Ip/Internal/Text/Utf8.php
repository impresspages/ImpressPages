<?php
/**
 * @package    ImpressPages
 *
 */

namespace Ip\Internal\Text;

/**
 * replaces special characters in a string
 * @package Library
 */
class Utf8
{
    /**
     *
     *  Returns $data encoded in UTF8. Very useful before json_encode as it fails if some strings are not utf8 encoded
     * @param mixed $dat array or string
     * @return array
     */
    public static function checkEncoding($dat)
    {
        if (is_string($dat)) {
            if (mb_check_encoding($dat, 'UTF-8')) {
                return $dat;
            } else {
                return utf8_encode($dat);
            }
        }
        if (is_array($dat)) {
            $answer = [];
            foreach ($dat as $i => $d) {
                $answer[$i] = self::checkEncoding($d);
            }
            return $answer;
        }
        return $dat;
    }

}