<?php
/**
 * @package   ImpressPages
 */

namespace IpUpdate\Library\Migration\To4_0;



class Helper
{
    private $dbPref;
    private $conn;

    public function __construct($cf, $conn)
    {
        $this->dbPref = $cf['db']['tablePrefix'];
        $this->conn = $conn;
        Db::init($this->conn);
    }

    /**
     * @param string $configFile
     * @throws CoreException
     */
    public function import($configFile)
    {
        $content = file_get_contents($configFile);
        $values = json_decode($content, true);
        if (!is_array($values)) {
            throw new \IpUpdate\Library\UpdateException("Can't parse configuration file: " . $configFile, \IpUpdate\Library\UpdateException::SQL);
        }
        foreach ($values as $key => $value) {
            $this->SetOption($key, $value);
        }
    }


    private function setOption($key, $value)
    {
        $parts = explode('.', $key, 2);
        if (!isset($parts[1])) {
            throw new \IpUpdate\Library\UpdateException("Option key must have plugin name separated by dot.", \IpUpdate\Library\UpdateException::SQL);
        }
        $this->setValue('Config', $parts[0] . '.' . $parts[1], $value);
    }

    /**
     * @param $pluginName
     * @param $key
     * @param $value
     */
    private function setValue($pluginName, $key, $value)
    {

        $sql = '
            INSERT INTO
                `'.$this->dbPref.'storage`
            SET
                `plugin` = :plugin,
                `key` = :key,
                `value` = :value
            ON DUPLICATE KEY UPDATE
                `plugin` = :plugin,
                `key` = :key,
                `value` = :value
        ';

        $params = array (
            ':plugin' => $pluginName,
            ':key' => $key,
            ':value' => $value
        );

        Db::execute($sql, $params);
    }

}