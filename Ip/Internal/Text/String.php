<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace Ip\Internal\Text;

/**
 * replaces special characters in a string
 * @package Library
 */
class String
{
    public static function mb_wordwrap($string, $width = 75, $break = "\n", $cut = false)
    {
        $words = explode(' ', $string);
        foreach ($words as &$word) {
            if (!$cut) {
                $regexp = '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){' . $width . ',}\b#U';
            } else {
                $regexp = '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){' . $width . '}#';
            }
            $string_length = mb_strlen($word, 'UTF-8');
            $cut_length = ceil($string_length / $width);
            $i = 1;
            $new_word = '';
            while ($i < $cut_length) {
                preg_match($regexp, $word, $matches);
                $new_string = $matches[0];
                $new_word .= $new_string . $break;
                $word = substr($word, strlen($new_string));
                $i++;
            }
            $word = $new_word . $word;
        }
        return join(' ', $words);
    }

}
