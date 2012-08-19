<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Ip\Event;



class ClearCache extends \Ip\Event{
    
    const SITE_CLEAR_CACHE = 'site.clearCache';
    
    public function __construct($object) {
        parent::__construct($object, self::SITE_CLEAR_CACHE, array());
    }    
}