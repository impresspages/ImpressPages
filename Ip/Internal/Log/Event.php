<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Log;

class System
{
    public function ipCronExecute($info)
    {
        if ($info['firstTimeThisMonth'] || $info['test']) {
            Db::deleteOldLogs(ipGetOption('Log.existenceInDays', '30'));
        }
    }
}