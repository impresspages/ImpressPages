<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\standard\breadcrumb;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;


/**
 * class to ouput current breadcrumb
 * @package ImpressPages
 */
class Module{

    /**
     * @return string HTML with links to website languages
     */
    static function generateBreadcrumb($separator, $showHome = true){
        global $site;
        
        $data = array (
            'homeUrl' => $site->generateUrl(),
            'breadcrumbElements' => $site->getBreadcrumb(),
            'separator' => $separator,
        );
        
        $breadcrumb = \Ip\View::create('view/breadcrumb.php', $data)->render();

        return $breadcrumb;

    }
}