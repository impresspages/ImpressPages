<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\administrator\repository;


/**
 * 
 * Centralized repository to store files. Often the same image needs to be used by many 
 * modules / widgets. This class handles these dependences. Use this module to add new files to global
 * files repository. Bind new modules to already existing files. When the file is not bind to any module,
 * it is automatically removed. So bind to existing files, undbind from them and don't whorry if some other
 * modules uses the same files. This class will take care.
 * 
 * @author Mangirdas
 *
 */
class Model{


    protected static $instance;

    protected function __construct()
    {

    }

    protected function __clone()
    {

    }

    /**
     * Get singleton instance
     * @return Model
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new Model();
        }

        return self::$instance;
    }

    
    /**
     * @deprecated
     * Add new file to the repository. Defined file will be duplicated. File name of duplicate will be returned.
     * 
     * @param string $file file to be added. Relative to BASE_DIR. E.g. file/tmp/file.doc
     * @param string $module module that uses this file (eg. standard/content_management)
     * @param int $id Unique identificator. Tells in which part of the module the file is used.
     * @return string where duplicated file is being stored. 
     **/
    public static function addFile($file, $module, $instanceId) {
        $destinationDir = FILE_DIR;
        $unocupiedName = \Library\Php\File\Functions::genUnoccupiedName($file, $destinationDir);
        copy(BASE_DIR.$file, BASE_DIR.$destinationDir.$unocupiedName);
        self::bindFile($destinationDir.$unocupiedName, $module, $instanceId);
        return $destinationDir.$unocupiedName;
    }
    
    public static function bindFile($file, $module, $instanceId) {
        $dbh = \Ip\Db::getConnection();
        $sql = "
        INSERT INTO
            `".DB_PREF."m_administrator_repository_file`
        SET
            `fileName` = :file,
            `module` = :module,
            `instanceId` = :instanceId,
            `date` = :date
        ";

        $params = array(
            'file' => $file,
            'module' => $module,
            'instanceId' => $instanceId,
            'date' => time()
        );

        $q = $dbh->prepare($sql);
        $q->execute($params);

        
    }

    public static function unbindFile($file, $module, $instanceId) {
        $dbh = \Ip\Db::getConnection();
        
        $sql = "
        DELETE FROM
            `".DB_PREF."m_administrator_repository_file`
        WHERE
            `fileName` = :file AND
            `module` = :module AND
            `instanceId` = :instanceId
        LIMIT
            1
        ";

        $params = array(
            'file' => $file,
            'module' => $module,
            'instanceId' => $instanceId
        );

        $q = $dbh->prepare($sql);
        $q->execute($params);


        $usages = self::whoUsesFile($file);
        if (empty($usages)) {
            $reflectionModel = ReflectionModel::instance();
            $reflectionModel->removeReflections($file);
        }

    }
    
    public static function isBind($file, $module, $instanceId) {
        $sql = "
                SELECT
                    *
                FROM
                    `".DB_PREF."m_administrator_repository_file`
                WHERE
                    `fileName` = '".mysql_real_escape_string($file)."' AND
                    `module` = '".mysql_real_escape_string($module)."' AND
                    `instanceId` = '".mysql_real_escape_string($instanceId)."'
                ";
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t bind new instance to the file '.$sql.' '.mysql_error(), Exception::DB);
        }
        
        if($lock = mysql_fetch_assoc($rs)) {
            return $lock['date'];
        } else {
            return false;
        }
        
    }
    
    public static function whoUsesFile($file){
        $sql = "
        SELECT
            *
        FROM
            `".DB_PREF."m_administrator_repository_file`
        WHERE
            `fileName` = :file
        ";

        $dbh = \Ip\Db::getConnection();
        $q = $dbh->prepare($sql);
        $q->execute(array(
            'file' => $file
        ));
        
        $answer = array();
        
        while($lock = $q->fetch()) {
            $answer[] = $lock;
        }
        return $answer;
    }
    
    private static function removeFile($file) {
        if (file_exists(BASE_DIR.$file) && !is_dir(BASE_DIR.$file) ) {
            unlink(BASE_DIR.$file);
        }
        
    }

    /**
     * Find all files bind to particular module
     */
    public function findFiles($module, $instanceId = null)
    {
        $dbh = \Ip\Db::getConnection();
        $sql = '
            SELECT
                *
            FROM
                `'.DB_PREF.'m_administrator_repository_file`
            WHERE
                `module` = :module
        ';

        $params = array (
            ':module' => $module
        );

        if ($instanceId !== null) {
            $sql .= ' and `instanceId` = :instanceId ';
            $params = array_merge($params, array('instanceId' => $instanceId));
        }

        $q = $dbh->prepare($sql);
        $q->execute($params);

        $answer = array();
        while ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            $answer[] = $lock;
        }
        return $answer;
    }
    
    
}