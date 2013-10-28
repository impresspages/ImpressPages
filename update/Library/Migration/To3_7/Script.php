<?php
/**
 * @package   ImpressPages
 */
//TODOX replace this script with actual from 3.7 release
namespace IpUpdate\Library\Migration\To3_7;


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

        $parameterImporter = new ParameterImporter($this->conn, $this->dbPref);
        $parameterImporter->importParameters('parameters.php');

        $this->removeFiles(array('admin.php', 'ip_backend_frames.php'));
    }

    private function removeFiles($files) {
        $fs = new \IpUpdate\Library\Helper\FileSystem();
        foreach($files as $file) {
             $fs->rm($this->cf['BASE_DIR'].$file);
        }
    }



    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getSourceVersion()
     */
    public function getSourceVersion()
    {
        return '3.6';
    }


    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getDestinationVersion()
     */
    public function getDestinationVersion()
    {
        return '3.7';
    }

}
