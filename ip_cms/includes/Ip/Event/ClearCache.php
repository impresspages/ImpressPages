<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Ip\Event;
if (!defined('CMS')) exit;


class ClearCache extends \Ip\Event{
    
    const SITE_CLEAR_CACHE = 'site.clearCache';
    
    private $oldUrl;
    private $newUrl;
    
    public function __construct($object, $oldUrl, $newUrl) {
        parent::__construct($object, self::SITE_CLEAR_CACHE, array());
    }    
    
    public function getOldUrl() {
        return $this->oldUrl;
    }
    
    public function getNewUrl() {
        return $this->newUrl;
    }

    public function urlChanged() {
        return $this->getOldUrl() !== $this->getNewUrl();
    }

}