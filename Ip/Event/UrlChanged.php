<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Event;

class UrlChanged extends \Ip\Event{

    const URL_CHANGED = 'site.urlChanged';

    private $oldUrl;
    private $newUrl;

    public function __construct($object, $oldUrl, $newUrl) {
        parent::__construct($object, self::URL_CHANGED, array());

        $oldUrlParts = explode('?', $oldUrl);
        $oldUrl = $oldUrlParts[0];

        $newUrlParts = explode('?', $newUrl);
        $newUrl = $newUrlParts[0];

        $this->oldUrl = $oldUrl;
        $this->newUrl = $newUrl;
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