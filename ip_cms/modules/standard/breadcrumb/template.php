<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\breadcrumb;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

class Template{

    public static function breadcrumb($breadCrumb, $separator = '') {
        $answer = '';
        foreach ($breadCrumb as $key => $element) {
            if($answer != '')
            $answer .= $separator;
            $answer .= '<a href="'.$element->getLink().'" title="'.htmlspecialchars($element->getPageTitle()).'">'.htmlspecialchars($element->getButtonTitle()).'</a>';
        }
        return $answer;
    }
}