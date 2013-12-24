<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid\Model;


/**
 * Table helper class designated to do Grid actions
 * @package Ip\Grid\Model
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

}