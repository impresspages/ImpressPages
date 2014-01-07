<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Log;

class System
{

    public function init()
    {
        ipDispatcher()->addEventListener('Ip.cronExecute', array($this, 'executeCron'));
    }

    public function executeCron($info)
    {
        if ($info['firstTimeThisMonth'] || $info['test']) {
            Db::deleteOldLogs(ipGetOption('Log.existenceInDays', '30'));
        }
    }
}