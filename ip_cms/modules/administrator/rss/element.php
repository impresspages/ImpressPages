<?php 
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace Modules\administrator\rss;


if (!defined('CMS')) exit;  

require_once (__DIR__.'/db.php');

/**
 *
 *
 * @package ImpressPages
 */

class Element extends \Frontend\Element {

  public function getLink() {
    global $site;
    return $site->generateUrl(null, $this->zoneName);
  }

  public function getDepth() {
    return 1;
  }


  protected function generateRss() {
    global $site;

    $rss = null;

    header('Content-type: application/rss+xml');

    $rss = $this->cachedRss();

    if(!$rss){
      $rss = $this->createRss();
    }
    return $rss;
  }

  protected function cachedRss() {
    global $site;
    $rss = Db::getRss($site->currentLanguage['id'], $this->id['zone_name'], $this->id['element_id']);
    $answer = $rss;
    return $answer;
  }

  public static function compareDate($element1, $element2) {
    if($element1->getCreatedOn() == $element2->getCreatedOn())
      return 0;
    elseif($element1->getCreatedOn() < $element2->getCreatedOn())
      return 1;
    else
      return -1;
  }




  protected function createRss() {
    global $site;
    global $parametersMod;
    $rss = '';
    $pages = array();
    if($this->id['zone_name'] !== null && $this->id['element_id'] !== null) {
      $elementId = $this->id['element_id'];
      $tmpObj = $site->getZone($this->id['zone_name']);
      $depth = $tmpObj->getElement($elementId)->getDepth();
      $pages = $this->getPages($this->id['zone_name'], $elementId);
    }elseif($this->id['zone_name'] !== null) {
      $pages = $this->getPages($this->id['zone_name']);
    }else {
      $zones = $site->getZones();
      foreach($zones as $key => $zone){
        $newPages = $this->getPages($zone->getName());
        if(is_array($newPages)){
          $pages = array_merge($pages, $this->getPages($zone->getName()));
        }
      }
    }
    $rssPages = array();
    foreach($pages as $key => $page) {
      if($page->getRss() && $page->getType() == 'default') {
        $rssPages[] = $page;
      }
    }



    usort($rssPages, array("\\Modules\\administrator\\rss\\Element", "compareDate"));

    $xmlEncode1 = array("&", "'", '"', '>', '<');
    $xmlEncode2   = array("&amp;", "&apos;", "&quot;", "&gt;", "&lt;");

    if($this->id['zone_name'] !== null && $this->id['element_id'] !== null) {
      $tmpElement = $site->getZone($this->id['zone_name'])->getElement($this->id['element_id']);
      $tmpTitle = $tmpElement->getPageTitle();
      $tmpDescription = $tmpElement->getPageTitle();
      $tmpLink = $tmpElement->getLink();
    }else {
      $tmpTitle = $parametersMod->getValue('administrator', 'rss', 'options', 'title');
      $tmpDescription = $parametersMod->getValue('administrator', 'rss', 'options', 'description');
      $tmpLink = BASE_URL;
    }

    $rss .= '<?xml version="1.0" encoding="UTF-8"?'.'>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>'.str_replace($xmlEncode1, $xmlEncode2, $tmpTitle).'</title>
		<link>'.str_replace($xmlEncode1, $xmlEncode2, $tmpLink).'</link>
		<description>'.str_replace($xmlEncode1, $xmlEncode2, $tmpDescription).'</description>
		<language>'.str_replace($xmlEncode1, $xmlEncode2, ($site->currentLanguage['code'])).'</language>
		<lastBuildDate>'.date("D, d M Y H:i:s").' GMT</lastBuildDate>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<generator>ImpressPages CMS</generator>
		<atom:link href="'.addslashes($site->getCurrentUrl()).'" rel="self" type="application/rss+xml" />';

    for($i=0; isset($rssPages[$i]) && $i<$parametersMod->getValue('administrator', 'rss', 'options', 'size'); $i++) {
      $page = $rssPages[$i];
      $rss .= '
		<item>
			<title>'.str_replace($xmlEncode1, $xmlEncode2, ($page->getPageTitle())).'</title>
			<link>'.str_replace($xmlEncode1, $xmlEncode2, ($page->getLink())).'</link>
			<description>'.str_replace($xmlEncode1, $xmlEncode2, ($page->getDescription())).'</description>
			<pubDate>'.str_replace($xmlEncode1, $xmlEncode2, date("D, d M Y H:i:s",strtotime($page->getCreatedOn()))).' GMT</pubDate>
			<guid isPermaLink="true">'.str_replace($xmlEncode1, $xmlEncode2, $page->getLink()).'</guid>
		</item>';
    }


    $rss.='
	</channel>
</rss>';

    Db::updateRss($site->currentLanguage['id'], $this->id['zone_name'], $this->id['element_id'], $rss);

    return $rss;
  }

  protected function getPages($zoneName, $parentId = null, $depth = 1, $maxDepth = 1000) {
    global $site;
    global $languages;
    $zone = $site->getZone($zoneName);
    $pages = $zone->getElements($site->currentLanguage['id'], $parentId);
    if($depth < $maxDepth) {
      if(is_array($pages)) {
        foreach($pages as $key => $page) {
          $pages = array_merge($pages, $this->getPages($zoneName, $page->getId()));
        }
      }
    }
    if(is_array($pages)) {
      return $pages;
    } else {
      return array();
    }
  }







  public function generateContent() {
    $answer = $this->generateRss();
    return $answer;
  }


  public function generateManagement() {
    return $this->generateContent();
  }
}




