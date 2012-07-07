<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Library\Migration;


abstract class General
{
    
    /**
     * @return \Library\Migration\Result
     */
    public abstract function process();
    
    /**
     * @return string
     */
    public abstract function getSourceVersion();
    
    /**
     * @return string
     */
    public abstract function getDestinationVersion();
    
    
}