<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model;


class Status
{

    public static function parse($statusVariable)
    {
        if (!empty($statusVariable[0]) && $statusVariable[0] == '#') {
            $statusVariable = substr($statusVariable, 1);
        }

        $variables = [];
        $parts = explode('&', $statusVariable);
        foreach ($parts as $part) {
            $tmp = explode('=', $part);
            if (isset($tmp[1])) {
                $key = $tmp[0];
                $val = urldecode($tmp[1]);
                if (substr($key, -5) == '_json') {
                    $key = substr($key, 0, -5);
                    $val = json_decode($val);
                }
                $variables[urldecode($key)] = $val;
            } else {
                $variables[urldecode($tmp[0])] = null;
            }
        }
        return $variables;
    }

    public static function build($variables)
    {
        return 'grid&' . http_build_query($variables);
    }


    /**
     * Get the depth of nesting
     * @param $statusVariables
     * @return int
     */
    public static function depth($statusVariables)
    {
        $depth = 1;
        while (isset($statusVariables['gridId' . $depth]) && isset($statusVariables['gridParentId' . $depth])) {
            $depth++;
        }
        return $depth;
    }


    public static function genSubgridVariables($curStatusVariables, $gridId, $gridParentId)
    {
        $newStatusVariables = [];
        $depth = Status::depth($curStatusVariables);

        for($i=1; $i<$depth; $i++) {
            $newStatusVariables['gridId' . $i] = $curStatusVariables['gridId' . $i];
            $newStatusVariables['gridParentId' . $i] = $curStatusVariables['gridParentId' . $i];
        }

        if ($gridId !== null) {
            $newStatusVariables['gridId' . $depth] = $gridId;
        }
        if ($gridParentId !== null) {
            $newStatusVariables['gridParentId' . $depth] = $gridParentId;
        }
        return $newStatusVariables;
    }
}
