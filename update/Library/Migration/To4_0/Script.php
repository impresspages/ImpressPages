<?php
/**
 * @package   ImpressPages
 */

namespace IpUpdate\Library\Migration\To4_0;


use IpUpdate\Library\UpdateException;

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

        $this->createPluginTable();


        //TODOX remove modules and permissions: sitemap, modules, newsletter, newsletter_subscribers, design, menu_management, log, email_queue, system

        //TODOX update zones to new associated plugins

        //TODOX remove newsletter zone

        //TODOX remove sitemap zone
    }


    protected function createPluginTable()
    {
        $dbh = $this->dbh;
        $sql = "
        CREATE TABLE IF NOT EXISTS `{$this->dbPref}plugin` (
          `name` varchar(30) NOT NULL,
          `version` decimal(10,2) NOT NULL,
          `active` int(11) NOT NULL DEFAULT '1',
          PRIMARY KEY (`name`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ";

        $q = $dbh->prepare($sql);
        $q->execute();
    }



    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getSourceVersion()
     */
    public function getSourceVersion()
    {
        return '3.7';
    }


    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getDestinationVersion()
     */
    public function getDestinationVersion()
    {
        return '4.0';
    }

}
