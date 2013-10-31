<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Event;

class PageMoved extends \Ip\Event{
    
    const SITE_PAGE_MOVED = 'site.pageMoved';
    

    private $pageId;
    private $sourceLanguageId;
    private $sourceZoneName;
    private $souceParentId;
    private $destinationLanguageId;
    private $destinationZoneName;
    private $destinationParentId;

    /**
     * 
     * @param objct $object who throws an event. Could be null
     * @param int $pageId page that has been moved
     * @param int $sourceLanguageId where page has been moved from
     * @param string $sourceZoneName where page has been moved from
     * @param int $sourceParentId where page has been moved from
     * @param int $sourcePosition starts at 0
     * @param int $destinationLanguageId where page has been moved to
     * @param string $destinationZoneName where page has been moved to
     * @param int $destinationParentId where page has been moved to
     * @param int $destinationPosition starts at 0
     */
    public function __construct($object, $pageId, $sourceLanguageId, $sourceZoneName, $sourceParentId, $sourcePosition, $destinationLanguageId, $destinationZoneName, $destinationParentId, $destinationPosition) {
        $this->pageId = $pageId;
        $this->sourceLanguageId = $sourceLanguageId;
        $this->sourceZoneName = $sourceZoneName;
        $this->pageParentId = $sourceParentId;
        $this->destinationLanguageId = $destinationLanguageId;
        $this->destinationZoneName = $destinationZoneName;
        $this->destinationParentId = $destinationParentId;
        parent::__construct($object, self::SITE_PAGE_MOVED, array());
    }
    
    public function getPageId() {
        return $this->pageId;
    }
    
    public function getSourceLanguageId() {
        return $this->sourceLanguageId;
    }
    
    public function getSourceZoneName() {
        return $this->sourceZoneName;
    }
    
    public function getSourceParentId() {
        return $this->sourceParentId;
    }
    
    public function getDestinationLanguageId() {
        return $this->destinationLanguageId;
    }
    
    public function getDestinationZoneName() {
        return $this->destinationZoneName;
    }
    
    public function getDestinationParentId() {
        return $this->destinationParentId;
    }
    
    
    
}