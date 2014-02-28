<?php


namespace Ip\Internal\System\Helper;


class SystemInfo
{

    /**
     * @return string
     */
    public static function getMemoryLimit()
    {
        $limit = ini_get('memory_limit');
        if (preg_match('(^(\d+)([BKMGT]))', $limit, $match)) {
            $shift = array('B' => 0, 'K' => 10, 'M' => 20, 'G' => 30, 'T' => 40);
            $limit = ($match[1] * (1 << $shift[$match[2]]));
        }

        return $limit;
    }

    public static function getMemoryLimitAsMb()
    {
        $limit = static::getMemoryLimit();

        if ($limit > 0) {
            return floor($limit / (1 >> 20));
        }

        return $limit;
    }

} 
