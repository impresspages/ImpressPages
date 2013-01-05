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
     * @param string $file - absolute path to image which reflection is requested
     * @param strong $desiredName - desired file name. If reflection is missing, service will try to create new one with name as possible similar to desired
     * @param Transform $transform - file transformation class
     * @return string - file name
     */
    public function getReflection($file, $desiredName = null, Transform $transform = null)
    {
        if (!$transform) {
            $transform = new Transform\None();
        }

        $fingerprint = $transform->getFingerprint();
        $reflection = $this->getReflectionRecord($file, $fingerprint);

        if (!$reflection) {
            $reflection = $this->createReflection($file, $desiredName, $transform);
        }

        return $reflection;
    }

    private function createReflection($file, $desiredName, $transform)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        echo $ext; exit;
        while(file_exists(BASE_DIR.FILE_DIR.$desiredName.$ext)) {

        }
    }


    private function getReflectionRecord($file, $transformFingerprint)
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
          transformFingerprint = :transformFingerprint
        ";

        $params = array(
            'original' => $file,
            'transformFingerprint' => $transformFingerprint
        );

        $q = $dbh->prepare($sql);
        $q->execute($params);

        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            return $lock['reflection'];
        }
    }



}