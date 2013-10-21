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

        $parameterImporter = new ParameterImporter($this->conn, $this->dbPref);
        $parameterImporter->importParameters('generalParameters.php');

        //$this->addDesignModule();

    }


    protected function addDesignModule()
    {
        $moduleModel = new ModuleModel($this->conn, $this->dbPref);
        $userModel = new UserModel($this->conn, $this->dbPref);
        $moduleGroup = $moduleModel->getModuleGroup('standard');
        $moduleId = $moduleModel->getModuleId('standard', 'design');
        if ($moduleId === false) {
            $groupModules = $moduleModel->getGroupModules($moduleGroup['id']);
            if (empty($groupModules)) {
                $newRowNumber = 1;
            } else {
                $lastModule = end($groupModules);
                $newRowNumber = $lastModule['row_number'] + 1;
            }

            $moduleId = $moduleModel->addModule($moduleGroup['id'], 'Design', 'design', true, true, true, '1.00', $newRowNumber);
            $users = $userModel->getUsers();
            foreach($users as $user){
                $userModel->addPermissions($moduleId, $user['id']);
            }
        }

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
