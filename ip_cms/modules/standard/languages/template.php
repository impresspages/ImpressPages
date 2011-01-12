<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Modules\standard\languages;
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

class Template{


  public static function languages($languages) {
    global $site;
    $answer = "\n";
    foreach ($languages as $key => $language) {
      if($language['id'] == $site->currentLanguage['id'])
        $answer .= '<a title="'.htmlspecialchars($language['d_long']).'" class="act" href="'.$site->generateUrl($language['id']).'">'.htmlspecialchars($language['d_short']).'</a>'."\n";
      else          
        $answer .= '<a title="'.htmlspecialchars($language['d_long']).'" href="'.$site->generateUrl($language['id']).'">'.htmlspecialchars($language['d_short']).'</a>'."\n";
    }
    $answer .= "\n";
    return $answer;
  }   
    

}