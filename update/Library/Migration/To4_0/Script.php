<?php
/**
 * @package   ImpressPages
 */

namespace IpUpdate\Library\Migration\To4_0;


use IpUpdate\Library\UpdateException;

class Script extends \IpUpdate\Library\Migration\General
{
    private $conn;
    /**
     * @var \PDO
     */
    private $dbh;
    private $dbPref;
    private $cf; // config

    public function process($cf)
    {
        $this->cf = $cf;
        $db = new \IpUpdate\Library\Model\Db();
        $conn = $db->connect($cf);
        $this->conn = $conn;
        $dbh = $db->connect($cf);
        $this->dbh = $dbh;

        $this->dbPref = $cf['db']['tablePrefix'];

        $this->createPluginTable();

        $helper = new Helper($cf, $this->conn);
        $helper->import(__DIR__ . '/options.json');

        //TODOX
        /*
        remove modules and permissions: sitemap, modules, newsletter, newsletter_subscribers, design, menu_management, log, email_queue, system

        update zones to new associated plugins

        remove newsletter zone

        remove sitemap zone
        communit/user zone to user zone
        replace administrator/search zone with Search zone in zones list
        */
        $this->createStorageTable();

        $this->migrateLogTable();
    }


    protected function createStorageTable()
    {
        $dbh = $this->dbh;
        $sql = "
        CREATE TABLE IF NOT EXISTS `{$this->dbPref}storage` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `plugin` varchar(40) NOT NULL,
    `key` varchar(100) NOT NULL,
    `value` text NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `pluginkey` (`plugin`,`key`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;
        ";

        $q = $dbh->prepare($sql);
        $q->execute();

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

    protected function migrateLogTable()
    {
        $sql = "SHOW FIELDS FROM `{$this->dbPref}log` WHERE `Field` = 'context'";
        $q = $this->dbh->prepare($sql);
        $q->execute();
        if ($q->fetchAll()) {
            return false; // Table is already updated
        }

        $q = $this->dbh->prepare("DROP TABLE IF EXISTS `{$this->dbPref}log`");
        $q->execute();

        $sql = "CREATE TABLE IF NOT EXISTS `{$this->dbPref}log` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `level` varchar(255) NOT NULL,
          `message` varchar(255) DEFAULT NULL,
          `context` mediumtext,
          PRIMARY KEY (`id`),
          KEY `time` (`time`),
          KEY `message` (`message`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;";
        $q = $this->dbh->prepare($sql);
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
