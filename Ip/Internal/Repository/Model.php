<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Repository;


/**
 * 
 * Centralized repository to store files. Often the same image needs to be used by many 
 * modules / widgets. This class handles these dependences. Use this module to add new files to global
 * files repository. Bind new modules to already existing files. When the file is not bind to any module,
 * it is automatically removed. So bind to existing files, undbind from them and don't whorry if some other
 * modules uses the same files. This class will take care.
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

    
    public static function bindFile($file, $plugin, $instanceId) {
        $row = array(
            'filename' => $file,
            'plugin' => $plugin,
            'instanceId' => $instanceId,
            'createdAt' => time()
        );
        ipDb()->insert('repositoryFile', $row);
    }

    public static function unbindFile($file, $plugin, $instanceId) {
        $condition = array(
            'fileName' => $file,
            'plugin' => $plugin,
            'instanceId' => $instanceId
        );

        $sql= 'DELETE FROM ' . ipTable('repositoryFile') . '
                WHERE filename = :fileName
                AND plugin = :plugin
                AND instanceId = :instanceId
                LIMIT 1'; // it is important to delete only one record

        ipDb()->execute($sql, $condition);

        $usages = self::whoUsesFile($file);
        if (empty($usages)) {
            $reflectionModel = ReflectionModel::instance();
            $reflectionModel->removeReflections($file);
        }

    }
    
    public static function whoUsesFile($file)
    {
        return ipDb()->selectAll('repositoryFile', '*', array('fileName' => $file));
    }
    
    /**
     * Find all files bind to particular module
     */
    public function findFiles($plugin, $instanceId = null)
    {
        $where = array (
            'plugin' => $plugin
        );

        if ($instanceId !== null) {
            $where['instanceId'] = $instanceId;
        }

        return ipDb()->selectAll('repositoryFile', '*', $where);
    }
    
    
}
