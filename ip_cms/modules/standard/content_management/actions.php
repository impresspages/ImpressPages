<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\standard\content_management;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;



class Actions {


    function makeActions() {
        if(isset($_REQUEST['action'])){
            switch($_REQUEST['action']){
                case 'sitemap_list':
                    $list = $this->getSitemapInList();
                    echo $list;
                    \Db::disconnect();
                    exit;                    
                    break;
            }

        }


    }



    public function getSitemapInList() {
        global $site;
        $answer = '';
        $answer .= '<ul id="ipSitemap">'."\n";

        $answer .= '<li><a href="'.BASE_URL.'">Home</a></li>'."\n";

        $languages = \Frontend\Db::getLanguages(true);//get all languages including hidden

        foreach($languages as $language) {
            $link = $site->generateUrl($language['id']);
            $answer .= '<li><a href="'.$link.'">'.htmlspecialchars($language['d_long']).' ('.htmlspecialchars($language['d_short']).')</a>'."\n";

            $zones = $site->getZones();
            if(sizeof($zones) > 0) {
                $answer .= '<ul>';
                foreach($zones as $key => $zone) {
                    $answer .= '<li><a href="'.$site->generateUrl($language['id'], $zone->getName()).'">'.$zone->getTitle().'</a>'."\n";
                    $answer .= $this->getPagesList($language, $zone);
                    $answer .= '</li>'."\n";
                }
                $answer .= '</ul>';

            }

            $answer .= '</li>'."\n";
        }


        $answer .= '<ul>'."\n";

        $answer = str_replace('?cms_action=manage', '', $answer);
        $answer = str_replace('&cms_action=manage', '', $answer);

        return $answer;
    }

    public function getPagesList($language, $zone, $parentElementId = null) {
        $answer = '';
        $pages = $zone->getElements($language['id'], $parentElementId, $startFrom = 0, $limit = null, $includeHidden = true, $reverseOrder = false);
        if($pages && sizeof($pages) > 0) {
            $answer .= '<ul>'."\n";
            foreach($pages as $key => $page) {
                $answer .= '<li><a href="'.$page->getLink(true).'">'.$page->getButtonTitle().'</a>';
                $answer .= $this->getPagesList($language, $zone, $page->getId());
                $answer .= '</li>';
            }
            $answer .= '</ul>'."\n";
        }
        return $answer;
    }



}



