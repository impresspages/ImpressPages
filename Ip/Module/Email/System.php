<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Email;

class System {



    function init(){
        $dispatcher = \Ip\ServiceLocator::getDispatcher();
        $dispatcher->bind('Cron.execute', array($this, 'executeCron'));
    }

    public function executeCron(\Ip\Event $e)
    {
        if($e->getValue('firstTimeThisMonth') || $e->getValue('test')) {
            Db::deleteOld(720);
        }

        if($e->getValue('firstTimeThisHour') || $e->getValue('test')){
            $queue = new Module();
            $queue->send();
        }

    }
}