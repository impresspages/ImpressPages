<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Library\Model;


class Db
{
    /**
     *
     * @param array $configuration configuration parsed using configuration parser
     */
    public function connect($cf)
    {
        try {
            return new \PDO('mysql:host='.$cf['DB_SERVER'].';dbname='.$cf['DB_DATABASE'], $cf['DB_USERNAME'], $cf['DB_PASSWORD']);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }

    }

}