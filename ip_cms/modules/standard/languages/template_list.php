<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */
namespace Modules\standard\languages;
if (!defined('CMS')) exit;

class TemplateList{


    public static function languages($languages) {
        global $site;
        $answer = "\n".'<ul>'."\n";
        foreach ($languages as $key => $language) {
            if($language->getVisible()){
                $actClass = ($language->getId() == $site->getCurrentLanguage()->getId()) ? ' class="current"' : '';
                $answer .= '  <li'.$actClass.'><a title="'.htmlspecialchars($language->getLongDescription()).'" href="'.$site->generateUrl($language->getId()).'">'.htmlspecialchars($language->getShortDescription()).'</a></li>'."\n";
            }
        }
        $answer .= '</ul>'."\n";
        return $answer;
    }


}