<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

namespace IpUpdate\Library\Migration\To2_4;


class Script extends \IpUpdate\Library\Migration\General{

    private $conn;
    private $dbPref;

    public function process($cf)
    {
        $conn = $db->connect($cf, \IpUpdate\Library\Model\Db::DRIVER_MYSQL);
        $this->conn = $conn;
        $this->dbPref = $cf['DB_PREF'];


        $sql = "update `".$this->dbPref."module` set `managed` = 0 where `name` = 'form'";
        $rs = mysql_query($sql, $this->conn);
        if (!$rs) {
            trigger_error("Can't remove form tab. ".$sql);
        }


        $sql = "ALTER TABLE  `".$this->dbPref."m_content_management_widget` CHANGE  `recreated`  `recreated` INT( 11 ) NULL COMMENT 'when last time the images were cropped freshly";
        $rs = mysql_query($sql, $this->conn);
        if (!$rs) {
            trigger_error("Can't update widget table. ".$sql);
        }


    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getSourceVersion()
     */
    public function getSourceVersion()
    {
        return '2.3';
    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getDestinationVersion()
     */
    public function getDestinationVersion()
    {
        return '2.4';
    }

}
