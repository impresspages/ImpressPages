<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2009 JSC Apro media.
 * @license   GNU/GPL, see ip_license.html
 */
namespace Modules\standard\languages;
if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

class Template{
    
  static function languages($languages) {
    global $site;
    $answer = '<ul class="languages">'."\n";
    foreach ($languages as $key => $language) {
      $actClass = ($language['id'] == $site->currentLanguage['id']) ? ' class="act"' : '';
      $answer .= '<li'.$actClass.'><a title="'.htmlspecialchars($language['d_long']).'" href="'.$site->generateUrl($language['id']).'">'.htmlspecialchars($language['d_short']).'</a></li>'."\n";
    }
    $answer .= '</ul>'."\n";
    return $answer;
  }   
}