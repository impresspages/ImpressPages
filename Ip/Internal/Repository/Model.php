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
class Model
{


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


    public static function bindFile($file, $plugin, $instanceId, $baseDir = 'file/repository/')
    {
        $row = array(
            'filename' => $file,
            'plugin' => $plugin,
            'instanceId' => $instanceId,
            'createdAt' => time(),
            'baseDir' => $baseDir
        );
        ipDb()->insert('repository_file', $row);
    }

    public static function unbindFile($file, $plugin, $instanceId, $baseDir = 'file/repository/')
    {
        $condition = array(
            'fileName' => $file,
            'plugin' => $plugin,
            'instanceId' => $instanceId,
            'baseDir' => $baseDir
        );

        $sql = 'DELETE FROM ' . ipTable('repository_file') . '
                WHERE filename = :fileName
                AND plugin = :plugin
                AND instanceId = :instanceId
                AND baseDir = :baseDir
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
        return ipDb()->selectAll('repository_file', '*', array('fileName' => $file));
    }

    /**
     * Find all files bind to particular module
     */
    public function findFiles($plugin, $instanceId = null)
    {
        $where = array(
            'plugin' => $plugin
        );

        if ($instanceId !== null) {
            $where['instanceId'] = $instanceId;
        }

        return ipDb()->selectAll('repository_file', '*', $where);
    }


    /**
     * Add file to the repository.
     * @param string $file absolute path to file in tmp directory
     * @param null|string $desiredName desired file name in repository.
     * @return string relative file name in repository
     * @throws \Ip\Exception
     */
    public function addFile($file, $desiredName)
    {
        if (!is_file($file)) {
            throw new \Ip\Exception("File doesn't exist");
        }

        if (strpos(realpath($file), realpath(ipFile('file/repository/'))) === 0) {
            throw new \Ip\Exception("Requested file (" . $file . ") is already in the repository");
        }

        $destination = ipFile('file/repository/');

        if ($desiredName === null) {
            $desiredName = basename($file); //to avoid any tricks with relative paths, etc.
        }

        $newName = \Ip\Internal\File\Functions::genUnoccupiedName($desiredName, $destination);
        copy($file, $destination . $newName);
        return $newName;
    }


}
