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
     * @param Transform\Base $transform - file transformation class
     * @return string - file name
     */
    public function getReflection($file, $desiredName = null, Transform\Base $transform = null)
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

    /**
     * @param string $file
     * @param string $desiredName
     * @param Transform\Base $transform
     */
    private function createReflection($file, $desiredName, Transform\Base $transform)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        $suffix = '';
        $suffixId = 0;
        while(file_exists(BASE_DIR.FILE_DIR.$desiredName.$suffix.$ext)) {
            $suffixId++;
            $suffix = '_'.$suffixId;
        }
        $reflection = FILE_DIR.$desiredName.$suffix.$ext;
        $transform->transform($file, BASE_DIR.$reflection);

        $transformFingerprint = $transform->getFingerprint();
        $this->storeReflectionRecord($file, $reflection, $transformFingerprint);

        return $reflection;
    }


    private function storeReflectionRecord($file, $reflection, $transformFingerprint)
    {
        $dbh = \Ip\Db::getConnection();
        $sql = "
        INSERT INTO
          ".DB_PREF."m_administrator_repository_reflection
        SET
          original = :original,
          reflection = :reflection,
          transformFingerprint = :transformFingerprint,
          created = :created
        ";

        $params = array(
            'original' => $file,
            'reflection' => $reflection,
            'transformFingerprint' => $transformFingerprint,
            'created' => time()
        );

        $q = $dbh->prepare($sql);
        $q->execute($params);

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