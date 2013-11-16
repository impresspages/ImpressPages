<?php
/*
 * ImpressPages CMS main frontend file
 *
 * @package ImpressPages
 *
 *
 */



/**
 *
 * Main frontend class. Each time the page is loaded the global instance of this class called $site is created.
 *
 * Access it anywhere by using:
 *
 * global $site;
 *
 * Use it to get information about the website:
 *
 * current language
 *
 * current zone
 *
 * current page
 *
 * current url
 *
 * ...
 *
 *
 */
class Site{



    /** @deprecated use getCurrentZone()->getName() instead */
    public $currentZone;

    /** @deprecated use getLanguages() instead */
    public $languages;

    /** @deprecated use getCurrentLanguage() instead */
    public $currentLanguage;


    /** string HTML or any other output. If is not null, it will be send to the output. If it is null, required page by request URL will be generated  */
    protected $output;

    /** int Revision of current page.  */
    protected $revision;


    public function __construct(){


    }



    /**
     *
     * @return string title of current page
     *
     */
    public function getTitle(){
        $curZone = ipGetCurrentZone();
        if (!$curZone) {
            return '';
        }
        $curEl =  $curZone->getCurrentPage();
        if($curEl && $curEl->getPageTitle() != '') {
            return $curEl->getPageTitle();
        } else {
            return $curZone->getTitle();
        }
    }

    /**
     *
     * @return string description of current page
     *
     */
    public function getDescription(){
        $curZone = ipGetCurrentZone();
        if (!$curZone) {
            return '';
        }
        $curEl =  $curZone->getCurrentPage();
        if($curEl && $curEl->getDescription() != '') {
            return $curEl->getDescription();
        } else {
            return $curZone->getDescription();
        }
    }



    /**
     *
     * @return string keywords of current page
     *
     */
    public function getKeywords(){
        $curZone = ipGetCurrentZone();
        if (!$curZone) {
            return '';
        }
        
        $curEl = $curZone->getCurrentPage();
        if($curEl && $curEl->getKeywords() != '') {
            return $curEl->getKeywords();
        } else {
            return $curZone->getKeywords();
        }
    }









}

