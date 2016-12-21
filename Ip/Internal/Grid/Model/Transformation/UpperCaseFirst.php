<?php
/**
 * @package   ImpressPages
 */


namespace Ip\Internal\Grid\Model\Transformation;

/**
 * Created by PhpStorm.
 * User: mangirdas
 * Date: 8/10/14
 * Time: 10:54 PM
 */

class UpperCaseFirst implements \Ip\Internal\Grid\Model\Transformation {
    public function transform($value, $options = [])
    {

        if (is_array($value)) {
            $answer = [];
            foreach($value as $item) {
                $answer[] = mbUpperFirst($item);
            }
            return $answer;
        }

        return mbUpperFirst($value);
    }

    protected function mbUpperFirst($value)
    {
        $first = mb_substr($value, 0, 1);
        $rest = mb_substr($value, 1);
        $result = mb_strtoupper($first) . $rest;
        return $result;
    }
}
