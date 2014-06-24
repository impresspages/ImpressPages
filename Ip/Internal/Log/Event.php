<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Log;


class Event
{

    /**
     * Cron execute
     *
     * @param array $info
     */
    public static function ipCronExecute($info)
    {
        if ($info['firstTimeThisMonth'] || $info['test']) {
            Db::deleteOldLogs(ipGetOption('Log.existenceInDays', '90'));
        }
    }

}
