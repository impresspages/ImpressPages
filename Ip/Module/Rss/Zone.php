<?php

/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Rss;


class Zone extends \Ip\Frontend\Zone {

    var $zoneName;
    var $db;
    var $standardModule;
    var $rssZoneKey;
    var $rssElementId;

    function __construct($properties) {
        global $site;
        global $parametersMod;
        $this->db = new Db();
        if (isset($site->urlVars[0]))
        $this->rssZoneKey = $site->urlVars[0];
        else
        $this->rssZoneKey = null;

        if (isset($site->urlVars[1]))
        $this->rssElementId = $site->urlVars[1];
        else
        $this->rssElementId = null;

        $this->rssLanguageId = $site->currentLanguage['id'];

        parent::__construct($properties);
    }

    /**
     * Find elements of this zone.
     * @return array Element
     */
    public function getElements($language = null, $parentElementId = null, $startFrom = 1, $limit = null, $includeHidden = false, $reverseOrder = null) {
        return array();
    }

    /**
     * @param int $elementId
     * @return Element
     */
    public function getElement($elementId) {
        return new Element(null, $this->name);
    }

    function findElement($urlVars, $getVars) {
        global $site;
        $contentLanguageId = $site->getCurrentLanguage()->getId();
        $contentZoneName = null;
        $contentElementId = null;
        if (
            ($this->rssZoneKey != null && !($site->getZone($this->rssZoneKey)))
            ||
            ($this->rssElementId != null && !$site->getZone($this->rssZoneKey)->getElement($this->rssElementId))
        ) {
            //returning false means error404
            return false;
        } else {
            $id = $contentLanguageId;

            if (isset($site->urlVars[0])) {
                $id .= '_' . $site->urlVars[0];
                $contentZoneName = $site->urlVars[0];
            } else {
                //do nothing
            }

            if (isset($site->urlVars[1])) {
                $id .= '_' . $site->urlVars[1];
                $contentElementId = $site->urlVars[1];
            } else {
                //do nothing
            }

            $element = new Element($id, $this->name);

            $element->contentLanguageId = $contentLanguageId;
            $element->contentZoneName = $contentZoneName;
            $element->contentElementId = $contentElementId;

            return $element;
        }
    }

    /**
     * @param zone
     * @return string URL to global or specified zone (element) RSS
     */
    public function generateRssLink($zone = null, $elementId = null) {
        global $site;

        if ($zone) {
            if ($elementId)
            return $site->generateUrl(null, $this->getName(), array($zone, $elemntId));
            else
            return $site->generateUrl(null, $this->getName(), array($zone));
        }else {
            return $site->generateUrl(null, $this->getName());
        }
    }

}
