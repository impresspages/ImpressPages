<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Log;

class System {

    public function init()
    {
        ipDispatcher()->bind('Cron.execute', array($this, 'executeCron'));
    }

    public function executeCron(\Ip\Event $e)
    {
        if ($e->getValue('firstTimeThisMonth') || $e->getValue('test')) {
            Db::deleteOldLogs(ipGetOption('Log.existenceInDays', '30'));
        }
    }
}