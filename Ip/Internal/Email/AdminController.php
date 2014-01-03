<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Email;



class AdminController extends \Ip\GridController
{
    protected function config()
    {
        return array (
            'type' => 'table',
            'allowUpdate' => false,
            'allowDelete' => false,
            'table' => 'email_queue',
            'actions' => array()
        );
    }

}