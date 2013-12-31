<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Content;


class RequestParser
{
    protected $currentLanguage;
    protected $languageUrl = null;
    protected $urlVars = null;
    protected $zoneUrl = null;
    protected $currentZoneName = null;
    protected $currentPage = null;


    public function getCurrentLanguage()
    {
        if (!$this->currentLanguage) {
            $this->parseUrl();
        }
        return $this->currentLanguage;
    }

    public function getUrlPath()
    {
        if ($this->urlVars === null) {
            $this->parseUrl();
        }
        return $this->urlVars;
    }


    /**
     * @return \Ip\Zone
     */
    public function getCurrentZone()
    {
        if ($this->currentZoneName === null) {
            $this->parseUrl();
        }
        return ipContent()->getZone($this->currentZoneName);
    }

    /**
     * @return \Ip\Page
     */
    public function getCurrentPage()
    {
        if ($this->currentPage === null) {
            $this->parseUrl();
        }
        return $this->currentPage;
    }

    private function parseUrl()
    {
        $languages = ipContent()->getLanguages();

        //check if admin
        if (!ipRequest()->isDefaultAction()) {
            //admin pages don't have zones
            if (!empty($_SESSION['ipLastLanguageId'])) {
                $this->currentLanguage = ipContent()->getLanguage($_SESSION['ipLastLanguageId']);
                if (!$this->currentLanguage) {
                    $this->currentLanguage = $languages[0];
                }
            } else {
                $this->currentLanguage = $languages[0];
            }
            $this->languageUrl = $this->currentLanguage->getUrl();
            $this->currentZoneName = false;
            return;
        }

        //find language
        $path = \Ip\ServiceLocator::request()->getRelativePath();
        $urlVars = explode('/', rtrim(parse_url($path, PHP_URL_PATH), '/'));
        if ($urlVars[0] == '') {
            array_shift($urlVars);
        }
        $this->urlVars = $urlVars;
        for ($i = 0; $i < sizeof($urlVars); $i++) {
            $urlVars[$i] = urldecode($urlVars[$i]);
        }
        if (ipGetOption('Config.multilingual') && !empty($urlVars[0])) {
            $languageUrl = urldecode(array_shift($urlVars));
            $this->urlVars = $urlVars;
            foreach ($languages as $language) {
                if ($language->getUrl() == $languageUrl) {
                    $this->currentLanguage = $language;
                    $this->languageUrl = $languageUrl;
                    break;
                }
            }
            //language not found. Set current language as first language from the database and set current zone to '' which means error 404
            if (!$this->currentLanguage) {
                $this->currentLanguage = $languages[0];
                $this->languageUrl = $this->currentLanguage->getId();
                $this->currentZoneName = '';
                return;
            }
        } else {
            $this->currentLanguage = $languages[0];
            $this->languageUrl = $this->currentLanguage->getUrl();
        }
        $_SESSION['ipLastLanguageId'] = $this->currentLanguage->getId();

        //find zone
        $zonesData = $this->getZonesData();
        if ($this->currentZoneName === null) {
            if (count($urlVars)) {
                $potentialZoneUrl = urldecode($urlVars[0]);
                foreach ($zonesData as $zoneData) {
                    if ($zoneData['url'] == $potentialZoneUrl) {
                        $this->zoneUrl = $potentialZoneUrl;
                        $this->currentZoneName = $zoneData['name'];
                        array_shift($urlVars);
                        $this->urlVars = $urlVars;
                        break;
                    }
                }
                if (!$this->zoneUrl) {
                    $zoneWithNoUrl = null;
                    foreach ($zonesData as $zoneData) {
                        if ($zoneData['url'] === '') {
                            $zoneWithNoUrl = $zoneData['name'];
                            $this->zoneUrl = '';
                            $this->currentZoneName = $zoneData['name'];
                            break;
                        }
                    }
                    if (!$zoneWithNoUrl) {
                        $this->currentZoneName = '';
                    }

                }
            } else {
                if (empty($zonesData)) {
                    throw new \Ip\CoreException('Please insert at least one zone');
                } else {
                    $firstZoneData = array_shift($zonesData);
                    $this->currentZoneName = $firstZoneData['name'];
                }
            }
        }


        //find current page

        $zone = ipContent()->getZone($this->currentZoneName);

        if ($zone) {
            $currentPage = $zone->getCurrentPage();
        } else {
            $currentPage = false;
        }

        if ($currentPage) {
            $this->currentPage = $currentPage;
        } else {
            $this->currentZoneName = '404';
            $this->currentPage = $this->currentPage = new \Ip\Page404(1, '404');
        }


    }

    protected function getZonesData()
    {
        return \Ip\Internal\ContentDb::getZones($this->getCurrentLanguage()->getId());
    }

}