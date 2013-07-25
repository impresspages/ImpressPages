<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\administrator\repository\Event;
if (!defined('CMS')) exit;


class ForbiddenExtensions extends \Ip\Event{
    //use this constant to catch event
    const EVENT_NAME =  'repository.forbiddenExtensions';
    private $forbidden = array();

    public function __construct($object) {
        $this->forbidden =  array('htaccess', 'htpasswd', 'php', 'php2','php3','php4','php5','php6','cfm','cfc','bat','exe','com','dll','vbs','js','reg','asis','phtm','phtml','pwml','inc','pl','py','jsp','asp','aspx','ascx','shtml','sh','cgi', 'cgi4', 'pcgi', 'pcgi5');
        parent::__construct($object, self::EVENT_NAME, array());
    }

    public function getForbiddenExtensions()
    {
        return $this->forbidden;
    }

    public function setForbiddenExtensions($forbidden) {
        $this->forbidden = $forbidden;
    }

}