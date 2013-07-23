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

        $this->createPageLayoutTable();

        $parameterImporter = new ParameterImporter($this->conn, $this->dbPref);

        $parameterImporter->importParameters('layoutParameters.php');
        $parameterImporter->importParameters('repositoryParameters.php');

        $this->cleanupRepositoryTable();

    }

    private function createPageLayoutTable()
    {
        $dbh = $this->dbh;
        $sql = "
        CREATE TABLE IF NOT EXISTS `{$this->dbPref}page_layout` (
          `group_name` varchar(128) NOT NULL,
          `module_name` varchar(128) NOT NULL,
          `page_id` int(11) unsigned NOT NULL,
          `layout` varchar(255) NOT NULL,
          UNIQUE KEY `group_name` (`group_name`,`module_name`,`page_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Custom page layouts';
        ";

        $q = $dbh->prepare($sql);
        $q->execute();
    }

    private function cleanupRepositoryTable()
    {
        $dbh = $this->dbh;
        $sql = "
        DELETE FROM `{$this->dbPref}m_administrator_repository_file` WHERE `module` = 'administrator/repository';
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
