<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model;


/**
 * Table helper class designated to do Grid actions
 * @package Ip\Internal\Grid\Model
 */
class Commands
{


    public static function setHtml($html)
    {
        return array(
            'command' => 'setHtml',
            'html' => $html
        );
    }


    public static function setHash($hash)
    {
        return array(
            'command' => 'setHash',
            'hash' => $hash
        );
    }


    public static function showMessage($message)
    {
        return array(
            'command' => 'showMessage',
            'message' => $message
        );
    }

}
