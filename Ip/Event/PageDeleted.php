<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Event;

class PageDeleted extends \Ip\Event{
    
    const SITE_PAGE_DELETED = 'site.pageDeleted';
    
    protected $zoneName;
    protected $pageId;

    public function __construct($object, $zoneName, $pageId) {
        $this->zoneName = $zoneName;
        $this->pageId = $pageId;
        parent::__construct($object, self::SITE_PAGE_DELETED, array());
    }
    
    public function getZoneName() {
        return $this->zoneName;
    }
    
    public function getPageId() {
        return $this->pageId;
    }

}