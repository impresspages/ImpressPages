<?php
/**
 *
 *
 */

namespace Ip\Frontend;


/**
 * Website language class.
 *
 */
class Language{
    /** string - eg. EN */
    public $shortDescription;
    /** string - eg. English */
    public $longDescription;
    /** string */
    public $url;
    /** string - RFC 4646 code. Eg. en, en-us */
    public $code;
    /** bool */
    public $visible;
    /** int */
    public $id;
    /** string - text direction */
    public $textDirection;


    /**
     *
     * @param $id int
     * @param $code string
     * @param $url string
     * @param $longDescription string
     * @param $shortDescription string
     * @return Language
     *
     */
    public function __construct($id, $code, $url, $longDescription, $shortDescription, $visible, $textDirection){
        $this->id = $id;
        $this->code = $code;
        $this->url = $url;
        $this->longDescription = $longDescription;
        $this->shortDescription = $shortDescription;
        $this->visible = $visible;
        $this->textDirection = $textDirection;
    }

    /**
     *
     * @return string Eg. English
     *
     */
    public function getTitle(){
        return $this->longDescription;
    }

    /**
     *
     * @return string Eg. en
     *
     */
    public function getAbbreviation(){
        return $this->shortDescription;
    }

    /**
     *
     * @return int
     *
     */
    public function getId(){
        return $this->id;
    }


    /**
     *
     * @return string Eg. en, en-us
     *
     */
    public function getCode(){
        return $this->code;
    }

    /**
     *
     * @return string
     *
     */
    public function getUrl(){
        return $this->url;
    }

    /**
     *
     * @return bool
     *
     */
    public function getVisible(){
        return $this->visible;
    }
    
    /**
     *
     * @return string
     *
     */
    public function getTextDirection(){
        return $this->textDirection;
    }

    
    /**
     * @returns boolean true if this language is the language of currently displpayed page 
     * Enter description here ...
     */
    public function getCurrent() {
        if ($this->getId() == ipGetCurrentLanguage()->getId()) {
            return true;
        } else {
            return false;
        }
    }
}