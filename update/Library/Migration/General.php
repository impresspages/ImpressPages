<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Library\Migration;


abstract class General
{
    
    /**
     * @return \Library\Migration\Result
     */
    public abstract function process();
    
    public abstract function toVersion();
    
}