<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Library\Model;


class Update
{

    private $cf;
    
    public function __construct($config)
    {
        $this->cf = $config;
    }

    public function proceed()
    {
        $tempStorage = new \IpUpdate\Library\Model\TempStorage($this->cf['BASE_DIR'].$this->cf['TMP_FILE_DIR'].'update/');
        
        if ($tempStorage->exist('inProgress')) {
            throw new \IpUpdate\Library\UpdateException("Update is in progress", \IpUpdate\Library\UpdateException::IN_PROGRESS, $data);
        }
        
        $tempStorage->setValue('inProgress', 1);
        
        $db = new Db();
        $conn = $db->connect($this->cf);
        
        
        //$tempStorage->remove('inProgress');
        
        $db->disconnect();
        
    }
}