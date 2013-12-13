<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Log;

class System
{

    public function init()
    {
        ipDispatcher()->addEventListener('Cron.execute', array($this, 'executeCron'));
    }

    public function executeCron($info)
    {
        if ($info['firstTimeThisMonth'] || $info['test']) {
            Db::deleteOldLogs(ipGetOption('Log.existenceInDays', '30'));
        }
    }
}