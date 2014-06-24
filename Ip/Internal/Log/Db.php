<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Log;


class Db
{

    /**
     * Delete old logs
     *
     * @param int $days
     */
    public static function deleteOldLogs($days)
    {
        $logTable = ipTable('log');
        ipDb()->execute(
            "delete from $logTable where  (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`time`)) > ?",
            array($days * 24 * 60 * 60)
        );
    }

}
