<?php 
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
 
namespace Modules\shop\currencies; 
 
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;


require_once (__DIR__.'/db.php');
  

class Module{
  
  public static function formatCurrency($amount, $currencyCode = null){
    global $site;
    if($currencyCode){
      require_once(__DIR__.'/db.php');
      $currency = Db::getCurrencyByCode($currencyCode);
    } else {
      $currency = self::getCurrentCurrency();
    }
    
    if($currency){ //required currency exist in database
      $amount = $amount * $currency['rate'];
      if(class_exists("\\NumberFormatter")){
        $nf = new \NumberFormatter($site->currentLanguage['code'], \NumberFormatter::CURRENCY);
        return($nf->formatCurrency($amount, $currency['code']));
      } else {
        $amount = round($amount, 2);
        return $amount.' '.$currency['code'];
      }
    }else{
      return $amount.' '.$currency['code'];
    }  
  }

  public static function getCurrentCurrency(){
    unset($_SESSION['modules']['shop']['currencies']['current']);
    if(isset($_SESSION['modules']['shop']['currencies']['current'])){
      return $_SESSION['modules']['shop']['currencies']['current'];
    }else{
      global $site;
      require_once(__DIR__.'/db.php');
      $currency = Db::getCurrencyByLanguage($site->currentLanguage['id']);
      if($currency){
        $_SESSION['modules']['shop']['currencies']['current'] = $currency;
      } else {
        $_SESSION['modules']['shop']['currencies']['current'] = Db::getDefaultCurrency();
      }
      return $_SESSION['modules']['shop']['currencies']['current'];
    }
  }
}