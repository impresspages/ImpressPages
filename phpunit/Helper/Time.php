<?php
    /**
     * @package   ImpressPages
     *
     *
     */

namespace PhpUnit\Helper;

class Time
{
    private static $changes;
    public static function changeTime($offsetSeconds)
    {
        if (! is_array(self::$changes)) {
            self::$changes = array();
        }
        self::$changes[] = $offsetSeconds;
        self::setTime(time() + $offsetSeconds);
    }

    public static function restoreTime()
    {
        if (is_array(self::$changes)) {
            $offsetSeconds = array_pop(self::$changes);
            self::setTime(time() - $offsetSeconds);
        } else {
            throw \Exception("Time hasn't been changed. Or it is already restored");
        }
    }

    protected static function setTime($seconds)
    {
        exec('sudo date +%N -s "@'.$seconds.'"');
    }

}