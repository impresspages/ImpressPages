<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\administrator\sitemap;



/**
 *
 *
 * @package ImpressPages
 */

class Element extends \Ip\Frontend\Element{

    public function getLink(){
        global $site;
        return $site->generateUrl(null, $this->zoneName);
    }

    public function getDepth(){
        return 1;
    }

    public function getButtonTitle(){
        global $parametersMod;
        return $parametersMod->getValue('administrator', 'sitemap', 'translations', 'sitemap');
    }



    function generateContent()
    {
        $site = \Ip\ServiceLocator::getSite();
        $parametersMod = \Ip\ServiceLocator::getParametersMod();
         
        $mappedZones = explode("\n", $parametersMod->getValue('administrator', 'sitemap', 'options', 'associated_zones'));
        $mappedZonesDepth = array();

        for($i=0; $i<sizeof($mappedZones); $i++){
            $begin = strrpos($mappedZones[$i], '[');
            $end =  strrpos($mappedZones[$i], ']');
            if($begin !== false && $end === strlen($mappedZones[$i]) - 1){
                $mappedZonesDepth[$i] = substr($mappedZones[$i], $begin + 1, - 1);
                $mappedZones[$i] = substr($mappedZones[$i], 0, $begin);
            } else {
                $mappedZonesDepth[$i] = -1; //unlimited depth
            }
        }



        $variables = array();


        $html = '';
        foreach($mappedZones as $key => $zoneName){
            if($zoneName != ''){
                $zone = $site->getZone($zoneName);
                if (!$zone) {
                    continue;
                }

                if($mappedZonesDepth[$key] == -1 ){ //unlimited depth
                    $depthLimit = 1000;
                } else {
                    $depthLimit = $mappedZonesDepth[$key];
                }


                $elements = $zone->getElements();
                $links = $this->getLinks($zone, $elements, 0, $depthLimit);

                $variables = array(
                    'links' => $links
                );
                $listHtml = \Ip\View::create('view/elements.php', $variables)->render();


                $variables = array(
                    'title' => $zone->getTitle(),
                    'elements' => $listHtml
                );

                if ($parametersMod->getValue('administrator', 'sitemap', 'options', 'include_zone_title')){
                    $variables['zoneTitle'] = $zone->getTitle();
                }


                $html .= \Ip\View::create('view/zone.php', $variables)->render();
            }
        }

        $variables['list'] = $html;
        $sitemapView = \Ip\View::create('view/sitemap.php', $variables);
        return $sitemapView->render();
    }

    protected function getLinks($zone, $elements, $curDepth, $maxDepth)
    {
        $links = array();
        if($maxDepth != null && $curDepth <= $maxDepth){
            if(is_array($elements) && sizeof($elements) > 0){
                foreach($elements as $element){
                    $newLink = array(
                        'title' => $element->getButtonTitle()
                    );

                    $children = $zone->getElements(null, $element->getId());
                    $childLinks = $this->getLinks($zone, $children, $curDepth+1, $maxDepth);
                    if (!empty($childLinks)) {
                        $newLink['childlinks'] = $childLinks;
                    }
                    $url = $element->getLink();
                    if ($url) {
                        $newLink['href'] = $url;
                    }
                    $links[] = $newLink;
                }
            }
        }
        return $links;



    }




}




