<?php
/**
 * @package   ImpressPages
 */

namespace IpUpdate\Library\Migration\To3_5;


use IpUpdate\Library\UpdateException;
use IpUpdate\Library\Migration\To3_5\ParameterImporter as ParameterImporter;

class Script extends \IpUpdate\Library\Migration\General
{
    private $conn;
    private $dbh;
    private $dbPref;
    private $cf; // config

    public function process($cf)
    {
        $this->cf = $cf;
        $db = new \IpUpdate\Library\Model\Db();
        $conn = $db->connect($cf, \IpUpdate\Library\Model\Db::DRIVER_MYSQL);
        $this->conn = $conn;
        $dbh = $db->connect($cf);
        $this->dbh = $dbh;

        $this->dbPref = $cf['DB_PREF'];



    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getSourceVersion()
     */
    public function getSourceVersion()
    {
        return '3.4';
    }


    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getDestinationVersion()
     */
    public function getDestinationVersion()
    {
        return '3.5';
    }

}
