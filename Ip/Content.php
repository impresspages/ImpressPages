<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;


/**
 *
 * Event dispatcher class
 *
 */
class Content {
    protected $currentLanguage;
    /**
     * @var \Ip\Frontend\Language[]
     */
    protected $languages;

    protected function __construct() {}
    protected function __clone(){}

    /**
     * Get singleton instance
     * @return Content
     */
    public static function instance()
    {
        return new self();
    }

    public function getCurrentLanguage()
    {
        if (!$this->currentLanguage) {
            $languageUrl = \Ip\ServiceLocator::getRequest()->getLanguageUrl();
            if ($languageUrl === null) {
                $allLanguages = $this->getLanguages();
                $this->currentLanguage = reset($allLanguages);
            } else {
                foreach ($this->languages as $language) {
                    if ($language->getUrl() == $languageUrl) {
                        $this->currentLanguage = $language;
                    }
                }
            }
        }
        return $this->currentLanguage;
    }

    /**
     *
     * @param $zoneName Name of zone you wish to get
     * @return \Ip\Frontend\Zone
     *
     */
    public function getZone($zoneName)
    {
        //if refactoring, keep in mind auto_error404 zone!!!
        if(isset($this->zones[$zoneName]))
        {
            if(!isset($this->zones[$zoneName]['object']))
            {
                //initialize zone object
                $tmpZone = $this->zones[$zoneName];
                if ($tmpZone['associated_group'] && $tmpZone['associated_module']) {
                    if (file_exists(\Ip\Config::oldModuleFile($tmpZone['associated_group'].'/'.$tmpZone['associated_module'].'/zone.php'))) {
                        require_once \Ip\Config::oldModuleFile($tmpZone['associated_group'].'/'.$tmpZone['associated_module'].'/zone.php');
                    } elseif (file_exists(\Ip\Config::oldModuleFile($tmpZone['associated_group'].'/'.$tmpZone['associated_module'].'/Zone.php'))) {
                        require_once \Ip\Config::oldModuleFile($tmpZone['associated_group'].'/'.$tmpZone['associated_module'].'/Zone.php');
                    }
                    eval ('$tmpZoneObject = new \\Modules\\'.$tmpZone['associated_group'].'\\'.$tmpZone['associated_module'].'\\Zone($tmpZone[\'name\']);');
                } else {
                    if ($tmpZone['associated_module']) {
                        $class = '\\Plugin\\' . $tmpZone['associated_module'] . '\\Zone';
                        if (class_exists($class)) {
                            $tmpZoneObject = new $class($tmpZone['name']);
                        } else {
                            $class = '\\Ip\\Module\\' . $tmpZone['associated_module'] . '\\Zone';
                            $tmpZoneObject = new $class($tmpZone['name']);
                        }
                    } else {
                        $tmpZoneObject = new \Ip\Frontend\DefaultZone($tmpZone);
                    }
                }

                $tmpZoneObject->setId($tmpZone['id']);
                $tmpZoneObject->setName($tmpZone['name']);
                $tmpZoneObject->setLayout($tmpZone['template']);
                $tmpZoneObject->setTitle($tmpZone['title']);
                $tmpZoneObject->setUrl($tmpZone['url']);
                $tmpZoneObject->setKeywords($tmpZone['keywords']);
                $tmpZoneObject->setDescription($tmpZone['description']);
                $tmpZoneObject->setAssociatedModuleGroup($tmpZone['associated_group']);
                $tmpZoneObject->setAssociatedModule($tmpZone['associated_module']);


                $this->zones[$zoneName]['object'] = $tmpZoneObject;
                //end initialize zone object
            }
            return $this->zones[$zoneName]['object'];
        } else {
            return false;
        }
    }

    /**
     *
     * @return array All registered zones. Use with caution. On big websites it can be very resource demanding operation because it requires all zone objects to be created.
     *
     */
    public function getZones(){
        $answer = array();
        foreach($this->zones as $zone){
            $answer[] = $this->getZone($zone['name']);
        }
        return $answer;
    }

    public function getCurrentZone()
    {
        $zones = \Ip\Frontend\Db::getZones($this->currentLanguage['id']);
        foreach ($zones as $key => $zone) {
            $this->zones[$zone['name']] = $zone;
        }

        if (sizeof($zones) == 0) {
            trigger_error('Please insert at least one zone.');
            \Ip\Internal\Deprecated\Db::disconnect();
            exit;
        }


        //find current zone
        if ($this->zoneUrl) {
            foreach ($zones as $key => $zone) {
                if($this->zoneUrl && $this->zoneUrl == $zone['url']) {
                    $this->currentZone = $zone['name'];
                    break;
                }
            }
        } else {
            foreach ($this->zones as $key => $zone) { //find first not empty zone.
                $this->currentZone = $key;
                if ($this->getZone($key)->getCurrentElement()) {
                    break;
                }
            }
        }

        if (!$this->currentZone) {
            $this->homeZone();
        }

        if (!$this->currentZone) {
            $this->error404();
        }
    }

    public function getCurrentPage()
    {

    }



    /**
     *
     * @return array - all website languages. Each element is an object Language
     *
     */
    public function getLanguages()
    {
        if ($this->languages === null) {
            $languages = \Ip\Frontend\Db::getLanguages(true);
            $this->languages = array();
            foreach($languages as $data){
                $this->languages[] = $this->createLanguage($data);
            }
        }
        return $this->languages;
    }



    /**
     *
     * @param data array from database
     * @return Language
     *
     *
     */
    private function createLanguage($data){
        $language = new \Ip\Frontend\Language($data['id'], $data['code'], $data['url'], $data['d_long'], $data['d_short'], $data['visible'], $data['text_direction']);
        return $language;
    }

}