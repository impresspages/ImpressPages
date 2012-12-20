<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\administrator\repository;



class ReflectionModel
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
     * @return ReflectionModel
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new ReflectionModel();
        }

        return self::$instance;
    }


    /**
     * @param string $file relative to website root
     * @param string $optionsKey
     * @return string
     */
    public function getReflection($file, $cropOptions)
    {
        if ($cropOptions) {
            $key = $this->getOptionsKey($cropOptions);
        } else {
            $key = '';
        }

        $reflection = $this->getReflectionRecord($file, $key);

        if (!$reflection) {
            $reflection = $this->createReflection($file, $key, $cropOptions);
        }

        return $reflection;
    }

    private function createReflection($file, $key, $cropOptions)
    {
        
    }


    private function getReflectionRecord($file, $optionsKey)
    {
        $dbh = \Ip\Db::getConnection();
        $sql = "
        SELECT
          reflection
        FROM
          ".DB_PREF."m_administrator_repository_reflection
        WHERE
          original = :original
          AND
          optionsKey = :optionsKey
        ";

        $params = array(
            'original' => $file,
            'optionsKey' => $optionsKey
        );

        $q = $dbh->prepare($sql);
        $q->execute($params);

        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            return $lock['reflection'];
        }
    }

    private function getOptionsKey($cropOptions)
    {
        $allOptions = array(
            $cropOptions->getSourceX1(),
            $cropOptions->getSourceX2(),
            $cropOptions->getSourceY1(),
            $cropOptions->getSourceY2(),
            $cropOptions->getDestinationWidth(),
            $cropOptions->getDestinationHeight()
        );
        $optionsStr = implode(' ', $allOptions);
        return md5($optionsStr);
    }

}