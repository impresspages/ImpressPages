<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Email;

class System {



    function init(){
        ipDispatcher()->bind('Cron.execute', array($this, 'executeCron'));
    }

    public function executeCron($info)
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