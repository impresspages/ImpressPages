<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Rss;


/**
 *
 *
 * @package ImpressPages
 */

class Element extends \Ip\Frontend\Element {
    public $contentZoneName;
    public $contentLanguageId;
    public $contentElementId;

    public function getLink() {
        global $site;
        return $site->generateUrl(null, $this->contentZoneName);
    }

    public function getDepth() {
        return 1;
    }


    protected function generateRss() {

        $rss = null;

        header('Content-type: application/rss+xml');

        $rss = $this->cachedRss();

        if (!$rss){
            $rss = $this->createRss();
        }
        return $rss;
    }

    protected function cachedRss() {
        global $site;
        $rss = Db::getRss($this->contentLanguageId, $this->contentZoneName, $this->contentElementId);
        return $rss;
    }

    public static function compareDate($element1, $element2) {
        if ($element1->getCreatedOn() == $element2->getCreatedOn()) {
            return 0;
        } elseif($element1->getCreatedOn() < $element2->getCreatedOn()) {
            return 1;
        } else {
            return -1;
        }
    }




    protected function createRss() {
        $site = \Ip\ServiceLocator::getSite();
        global $parametersMod;
        $rss = '';
        $pages = array();
        if($this->contentZoneName !== null && $this->contentElementId !== null) {
            $elementId = $this->contentElementId;
            $pages = $this->getPages($this->contentZoneName, $elementId);
        } elseif ($this->contentZoneName !== null) {
            $pages = $this->getPages($this->contentZoneName);
        } else {
            $zones = $site->getZones();
            foreach ($zones as $zone) {
                $newPages = $this->getPages($zone->getName());
                if(is_array($newPages)){
                    $pages = array_merge($pages, $this->getPages($zone->getName()));
                }
            }
        }
        $rssPages = array();
        foreach ($pages as $page) {
            if ($page->getRss() && $page->getType() == 'default') {
                $rssPages[] = $page;
            }
        }



        usort($rssPages, array("\\Ip\\Module\\Rss\\Element", "compareDate"));

        $xmlEncode1 = array("&", "'", '"', '>', '<');
        $xmlEncode2   = array("&amp;", "&apos;", "&quot;", "&gt;", "&lt;");

        if ($this->contentZoneName !== null && $this->contentElementId !== null) {
            $tmpElement = $site->getZone($this->contentZoneName)->getElement($this->contentElementId);
            $tmpTitle = $tmpElement->getPageTitle();
            $tmpDescription = $tmpElement->getPageTitle();
            $tmpLink = $tmpElement->getLink();
        } else {
            $tmpTitle = $parametersMod->getValue('Rss.title');
            $tmpDescription = $parametersMod->getValue('Rss.description');
            $tmpLink = \Ip\Config::baseUrl('');
        }

        $rss .= '<?xml version="1.0" encoding="UTF-8"?'.'>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>'.str_replace($xmlEncode1, $xmlEncode2, $tmpTitle).'</title>
		<link>'.str_replace($xmlEncode1, $xmlEncode2, $tmpLink).'</link>
		<description>'.str_replace($xmlEncode1, $xmlEncode2, $tmpDescription).'</description>
		<language>'.str_replace($xmlEncode1, $xmlEncode2, ($site->getLanguageById($this->contentLanguageId)->getCode())).'</language>
		<lastBuildDate>'.date("D, d M Y H:i:s").' GMT</lastBuildDate>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<generator>ImpressPages CMS</generator>
		<atom:link href="'.addslashes($site->getCurrentUrl()).'" rel="self" type="application/rss+xml" />';

        for($i=0; isset($rssPages[$i]) && $i<$parametersMod->getValue('Rss.size'); $i++) {
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

        Db::updateRss($this->contentLanguageId, $this->contentZoneName, $this->contentElementId, $rss);

        return $rss;
    }

    protected function getPages($zoneName, $parentId = null, $depth = 1, $maxDepth = 1000) {
        global $site;
        $zone = $site->getZone($zoneName);
        $pages = $zone->getElements($this->contentLanguageId, $parentId);
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



}




