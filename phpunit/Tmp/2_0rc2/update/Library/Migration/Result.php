<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Library\Migration;


abstract class Result
{
    
    
    public abstract function process();
    
    public abstract function getTargetVersion();
    
    public abstract function getSourceVersion();
    
    
}