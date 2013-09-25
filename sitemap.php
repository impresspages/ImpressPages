<?php
/**
 *
 * ImpressPages CMS dynamic sitemap
 *
 * This file generates sitemap index and sitemaps.
 *
 * @package ImpressPages
 *
 *
 */

/** @private */

/** @private */


if (!defined('SITEMAP_MAX_LENGTH')) {
    define('SITEMAP_MAX_LENGTH', 50000);
}


if (!defined('CMS')) {
    define('CMS', true); // make sure other files are accessed through this file.
}
if (!defined('BACKEND')) {
    define('BACKEND', true); // make sure other files are accessed through this file.
}
if (!defined('SITEMAP')) {
    define('SITEMAP', true);
}

if(is_file(__DIR__.'/ip_config.php')) {
    require (__DIR__.'/ip_config.php');
} else {
    require (__DIR__.'/../ip_config.php');
}

error_reporting(E_ALL|E_STRICT);
if (DEVELOPMENT_ENVIRONMENT){ 
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}


require_once(BASE_DIR.FRONTEND_DIR.'init.php');

if(\Db::connect()){
    $log = new \Modules\administrator\log\Module();

    try {
        $dispatcher = new \Ip\Dispatcher();

        $parametersMod = new ParametersMod();
        $session = new \Frontend\Session();


        $site = new \Site();
        $site->init();
        $dispatcher->notify(new \Ip\Event($site, 'site.afterInit', null));
        

        $sitemap = new Sitemap();

        if(isset($_GET['nr']) && isset($_GET['lang']) && isset($_GET['zone'])){
            echo $sitemap->getSitemap($_GET['zone'], $_GET['lang'], $_GET['nr']);
        }else{
            echo $sitemap->getSitemapIndex();
        }


        \Db::disconnect();
    } catch (\Exception $e) {
        $log->log('System', 'Fatal error', $e->getMessage().' in '.$e->getFile().':'.$e->getLine());
        throw $e;
    }

}else   trigger_error('Database access');



/**
 * Sitemap index and sitemap generation class
 * @package ImpressPages
 */
class sitemap{
    var $mappedZones;

    function __construct(){
        global $parametersMod;
        $this->mappedZones = array();
        $mappedZones = explode("\n", $parametersMod->getValue('standard', 'configuration', 'advanced_options', 'xml_sitemap_associated_zones'));

        $mapped_zone = null;
        for($i=0; $i<sizeof($mappedZones); $i++){
            $begin = strrpos($mappedZones[$i], '[');
            $end =  strrpos($mappedZones[$i], ']');
            if($begin !== false && $end === strlen($mappedZones[$i]) - 1){
                $tmp_name = substr($mappedZones[$i], 0, $begin);
                $this->mappedZones[$tmp_name] = substr($mappedZones[$i], $begin + 1, - 1);
            }else{
                $this->mappedZones[$mappedZones[$i]] = -1;
            }

        }
         
    }

    /**
     * Generates sitemap XML
     * @param int $nr Number of sitemap. Big sites are split into several sitemaps. Begining from 0.
     * @return string Sitemap XML
     */
    function getSitemap($zone, $languageId, $nr){
        global $parametersMod;
        global $site;

        if (!isset($this->mappedZones[$zone]) || $site->getZone($zone) == false) {
            header('HTTP/1.0 404 Not Found');
            \Db::disconnect();
            exit;
        }


        header('Content-type: application/xml; charset="'.CHARSET.'"',true);
         


        $answer = '';
        $answer .= '<'.'?xml version="1.0" encoding="'.CHARSET.'"?'.'>
  		<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  	
  	';
         
         
         
        if($this->mappedZones[$zone] == -1) //unlimited depth
        $pages = $this->getPages($site->getZone($zone), $languageId);
        else
        $pages = $this->getPages($site->getZone($zone), $languageId, $this->mappedZones[$zone]);
        //var_dump($pages);
         
        for($i=$nr*SITEMAP_MAX_LENGTH; $i<($nr+1)*SITEMAP_MAX_LENGTH; $i++){
            if(isset($pages[$i])){
                $answer .= '
  			   <url>
  			      <loc>'.$pages[$i]->getLink().'</loc>
  			';
                if ($pages[$i]->getLastModified()) {
                    $answer .= '<lastmod>'.substr($pages[$i]->getLastModified(), 0, 10).'</lastmod>
    			';
                }
                if($frequency = $pages[$i]->getModifyFrequency()){
                    $tmp_freq = '';
                    if($frequency < 60*30) //30 min
                    $tmp_freq = 'always';
                    elseif($frequency < 60*60) //1 hour
                    $tmp_freq = 'hourly';
                    elseif($frequency < 60*60*24) //1 day
                    $tmp_freq = 'daily';
                    elseif($frequency < 60*60*24*7) //1 week
                    $tmp_freq = 'weekly';
                    elseif($frequency < 60*60*24*30) //1 month
                    $tmp_freq = 'monthly';
                    elseif($frequency < 60*60*24*360*2) //2 years
                    $tmp_freq = 'yearly';
                    else
                    $tmp_freq = 'never';

                     
                    $answer .= '<changefreq>'.$tmp_freq.'</changefreq>
  				';
                }
                if ($tmpPriority = $pages[$i]->getPriority()) {
                    $answer .= '<priority>'.$tmpPriority.'</priority>
    			';
                }
                $answer .= '
  			   </url>
  			';
            }

        }
         
        $answer .= '
  	</urlset>';
        return $answer;
    }


    /**
     * Generates array of all website pages
     * @return array ('link', 'last_modified', 'modify_frequency', 'priority')
     */
    function getPages($zone, $languageId, $maxDepth = 1000, $parentId = null, $curDepth = 1){
        global $site;
        $pages = array();
        if ($curDepth <= $maxDepth) {
            $tmpElements = $zone->getElements($languageId, $parentId);
            foreach ($tmpElements as $key => $element) {
                if ($element->getType() == 'default') {
                    $pages[] = $element;
                }
                $pages = array_merge($pages, $this->getPages($zone, $languageId, $maxDepth, $element->getId(), $curDepth+1));
            }
        }
        return $pages;
    }





    /**
     * @return string sitemap index XML
     */
    function getSitemapIndex(){
        global $site;

        header('Content-type: application/xml; charset="'.CHARSET.'"',true);

        $answer = '';

        $answer .= '<'.'?xml version="1.0" encoding="'.CHARSET.'"?'.'>
  <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach($this->mappedZones as $curZone => $curDepth){
            //count all page is to expensive operation.
            /*if($curDepth == -1) //unlimited depth
            $count = $this->getPagesCount($site->getZone($curZone));
            else
            $count = $this->getPagesCount($site->getZone($curZone), $curDepth);
            for($i=0; $i<$count/SITEMAP_MAX_LENGTH; $i++){
            $answer .= '
            <sitemap>
            <loc>'.BASE_URL.'sitemap.php?zone='.$curZone.'&amp;nr='.$i.'</loc>
            </sitemap>
            ';
            }*/
            foreach($site->languages as $key => $language){
                if($language['visible']){
                    $answer .= '
           <sitemap>
              <loc>'.BASE_URL.'sitemap.php?zone='.$curZone.'&amp;lang='.$language['id'].'&amp;nr=0</loc>    
           </sitemap>
           ';  
                }
            }
        }

        $answer .= '</sitemapindex>
    ';
        return $answer;
    }


    /**
     * @return int active and visible pages count in all zones
     */
    function getPagesCount($zone, $maxDepth = 1000, $parentId = null, $curDepth = 1){
        global $site;
        $count = 0;
        if ($curDepth <= $maxDepth) {
            foreach($site->languages as $key => $language){
                if($language['visible']){
                    $tmpElements = $zone->getElements($language['id'], $parentId);
                    foreach ($tmpElements as $key => $element) {
                        if ($element->getType() == 'default') {
                            $count++;
                        }
                        $count += $this->getPagesCount($zone, $maxDepth, $element->getId(), $curDepth+1);
                    }
                }
            }
        }
        return $count;
    }




}





?>