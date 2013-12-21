<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Email;



class AdminController extends \Ip\Grid1\Controller
{
    protected function config()
    {
        return array (
            'type' => 'table',
            'table' => 'm_administrator_email_queue'
        );
    }

}