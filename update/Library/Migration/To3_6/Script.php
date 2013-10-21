<?php
/**
 * @package   ImpressPages
 */

namespace IpUpdate\Library\Migration\To3_6;


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

        if (!is_writable($cf['BASE_DIR'] . $cf['THEME_DIR'])) {
            $errorData = array (
                'file' => $cf['BASE_DIR'] . $cf['THEME_DIR']
            );
            throw new UpdateException("Please make theme dir writable.", UpdateException::WRITE_PERMISSION, $errorData);
        }


        $this->addDesignModule();
        $parameterImporter = new ParameterImporter($this->conn, $this->dbPref);
        $parameterImporter->importParameters('generalParameters.php');

        $this->addDesignDatabaseTable();

        $this->removeThemeModule();
    }

    protected function removeThemeModule()
    {
        $moduleModel = new ModuleModel($this->conn, $this->dbPref);
        $moduleId = $moduleModel->getModuleId('administrator', 'theme');

        if (!$moduleId) {
            return;
        }

        //delete theme user permissions
        $sql = '
            DELETE FROM `'.$this->cf['DB_PREF'].'user_to_mod` where module_id = :module_id
        ';
        $q = $this->dbh->prepare($sql);
        $data = array(
            'module_id' => $moduleId
        );
        $q->execute($data);

        //delete module
        $sql = '
            DELETE FROM `'.$this->cf['DB_PREF'].'module` where id = :module_id
        ';
        $q = $this->dbh->prepare($sql);
        $data = array(
            'module_id' => $moduleId
        );
        $q->execute($data);

    }


    protected function addDesignDatabaseTable()
    {
        $sql = '
            CREATE TABLE IF NOT EXISTS `'.$this->cf['DB_PREF'].'m_design` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `theme` varchar(100) NOT NULL,
              `name` varchar(100) NOT NULL,
              `value` varchar(255) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `theme` (`theme`,`name`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        ';
        $q = $this->dbh->prepare($sql);
        $q->execute();
    }


    protected function addDesignModule()
    {
        $moduleModel = new ModuleModel($this->conn, $this->dbPref);
        $userModel = new UserModel($this->conn, $this->dbPref);
        $moduleGroup = $moduleModel->getModuleGroup('standard');
        $moduleId = $moduleModel->getModuleId('standard', 'design');
        if ($moduleId === false) {
            //create module
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
        return '3.5';
    }


    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getDestinationVersion()
     */
    public function getDestinationVersion()
    {
        return '3.6';
    }

}
