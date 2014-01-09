<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Email;

class Event
{
    public static function ipCronExecute($info)
    {
        if ($info['firstTimeThisMonth'] || $info['test']) {
            Db::deleteOld(720);
        }

        if ($info['firstTimeThisHour'] || $info['test']) {
            $queue = new Module();
            $queue->send();
        }
    }
}