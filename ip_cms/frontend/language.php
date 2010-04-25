<?php 
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
 
namespace Frontend;
 
if (!defined('CMS')) exit;  


/**
 *   
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
  public function __construct($id, $code, $url, $longDescription, $shortDescription){
    $this->id = $id;
    $this->code = $code;
    $this->url = $url;
    $this->longDescription = $longDescription;
    $this->shortDescription = $shortDescription;
  }

  /**
   * 
   * @return string Eg. English
   * 
   */
  public function getLongDescription(){
    return $this->longDescription;
  }

  /**
   * 
   * @return string Eg. en
   * 
   */
  public function getShortDescription(){
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
  
  
}