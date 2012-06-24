<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Library;


class Service
{
    private $cf;
    private $installationDir;

    public function __construct($installationDir)
    {
        $this->installationDir = $installationDir;
        $configurationParser = new Model\ConfigurationParser();
        $this->cf = $configurationParser->parse($installationDir);
    }


    public function update ($destinationVersion)
    {
        $db = new Model\Db($this->cf);
        $conn = $db->connect();


        $db->disconnect();
    }

    public function getCurrentVersion()
    {
        $db = new Model\Db();
        $dbh = $db->connect($this->cf);

        $sql = '
            SELECT
                value
            FROM
                `'.str_replace('`', '', $this->cf['DB_PREF']).'variables`
            WHERE
                `name` = :name
        ';
        
        $params = array (
            ':name' => 'version'
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);

        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            $answer = $lock['value'];
            return $answer;
        } else {
            throw new Exception("Can't find installation vesrion");
        }
        
        $db->disconnect();
    }

    public function availableVersions()
    {

    }

     
}