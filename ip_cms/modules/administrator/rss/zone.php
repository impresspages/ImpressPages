<?php

/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\rss;

if (!defined('FRONTEND') && !defined('BACKEND'))
  exit;

require_once (__DIR__ . '/db.php');
require_once (__DIR__ . '/element.php');

class Zone extends \Frontend\Zone {

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
    if (
            ($this->rssZoneKey != null && !($site->getZone($this->rssZoneKey)))
            ||
            ($this->rssElementId != null && !$site->getZone($this->rssZoneKey)->getElement($this->rssElementId))

      )return false;  //returning false means error404
 else {

      $id = array();

      $id['language_id'] = $site->currentLanguage['id'];

      if (isset($site->urlVars[0]))
        $id['zone_name'] = $site->urlVars[0];
      else
        $id['zone_name'] = null;

      if (isset($site->urlVars[1]))
        $id['element_id'] = $site->urlVars[1];
      else
        $id['element_id'] = null;

      $answer = new Element($id, $this->name);

      return $answer;
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
