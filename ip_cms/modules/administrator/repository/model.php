<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\administrator\repository;
if (!defined('CMS')) exit;



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

    
    /**
     * Add new file to the repository. Defined file will be duplicated. File name of dupliace will be returned. 
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
        $sql = "
        INSERT INTO
            `".DB_PREF."m_administrator_repository_file`
        SET
            `fileName` = '".mysql_real_escape_string($file)."',
            `module` = '".mysql_real_escape_string($module)."',
            `instanceId` = '".mysql_real_escape_string($instanceId)."',
            `date` = '".time()."'
        ";
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t bind new instance to the file '.$sql.' '.mysql_error(), Exception::DB);
        }
        
    }
    
    public static function unbindFile($file, $module, $instanceId) {

        
        $sql = "
        DELETE FROM
            `".DB_PREF."m_administrator_repository_file`
        WHERE
            `fileName` = '".mysql_real_escape_string($file)."' AND
            `module` = '".mysql_real_escape_string($module)."' AND
            `instanceId` = '".mysql_real_escape_string($instanceId)."'
        LIMIT
            1
        ";
        //delete operation limited to one, because there might exist many files bind to the same instance of the same module. For example: gallery widget adds the same photo twice.
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t file instance '.$sql.' '.mysql_error(), Exception::DB);
        }
        $whoUses = self::whoUsesFile($file);
        
        if (count($whoUses) == 0) {
            self::removeFile($file);
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
            `fileName` = '".mysql_real_escape_string($file)."'
        ";
        
        $rs = mysql_query($sql);
        if (!$rs){
            throw new Exception('Can\'t bind new instance to the file '.$sql.' '.mysql_error(), Exception::DB);
        }
        
        $answer = array();
        
        while($lock = mysql_fetch_assoc($rs)) {
            $answer[] = $lock;
        }
        return $answer;
    }
    
    private static function removeFile($file) {
        if (file_exists(BASE_DIR.$file) && !is_dir(BASE_DIR.$file) ) {
            $deletedDir = FILE_DIR.'deleted/';
            if (!file_exists($deletedDir) || !is_dir($deletedDir)) {
                mkdir($deletedDir);
            }
            $newFileName = \Library\Php\File\Functions::genUnoccupiedName($file, $deletedDir);

            $success = copy(BASE_DIR.$file, BASE_DIR.$deletedDir.$newFileName);
            if (!$success) {
                throw new \Exception('Can\'t unbind file from repository: '.BASE_DIR.$file);
            }
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