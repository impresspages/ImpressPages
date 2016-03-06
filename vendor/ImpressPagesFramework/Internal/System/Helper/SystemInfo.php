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
            return floor($limit / 1048576);
        }

        return $limit;
    }

    /**
     * Allocates memory (if required).
     *
     * @param int $bytesRequired
     * @param int $extra in bytes
     * @return bool|null true if enough memory, false if could not allocate, null if there is no way to know
     */
    public static function allocateMemory($bytesRequired, $extra = 0x1000000) //~10Mb extra
    {
        $memoryLimit = \Ip\Internal\System\Helper\SystemInfo::getMemoryLimit();

        if (!function_exists('memory_get_usage') && $memoryLimit !== '-1') {
            //try to allocate as much as we can
            ini_set('memory_limit', '100M');
            ini_set('memory_limit', '150M');
            ini_set('memory_limit', '200M');
            ini_set('memory_limit', '500M');
            return null; // We can't calculate how much memory should be allocated
        }

        if ('-1' == $memoryLimit) { // unlimited
            return true;
        }

        $memoryRequired = memory_get_usage() + $bytesRequired;
        if ($memoryRequired < $memoryLimit) {
            return true;
        }

        $megabytesNeeded = ceil($memoryRequired + $extra / 0x100000) . 'M';
        if (!ini_set('memory_limit', $megabytesNeeded)) {
            ipLog()->warning(
                'Could not allocate enough memory. Please increase memory limit to {memoryNeeded}',
                array('memoryNeeded' => $megabytesNeeded)
            );
            return false;
        }

        return true;
    }

}
