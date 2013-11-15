<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Response;

/**
 *
 * Event dispatcher class
 *
 */
class Status404 extends \Ip\Response {
//TODOX fix
//is_file(\Ip\Config::themeFile('404.php')) ? '404.php' : 'main.php'

    public function send()
    {
        $event = new \Ip\Event($this, 'site.beforeError404', null);
        \Ip\ServiceLocator::getDispatcher()->notify($event);
        if (!$event->getProcessed()) {
            \Ip\ServiceLocator::getDispatcher()->notify(new \Ip\Event($this, 'site.error404', null));
        }
    }
}


