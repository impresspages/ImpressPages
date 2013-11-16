<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Breadcrumb;


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
            'homeUrl' => \Ip\Internal\Deprecated\Url::generate(),
            'breadcrumbElements' => ipGetBreadcrumb(),
            'separator' => $separator,
        );
        
        $breadcrumb = \Ip\View::create('view/breadcrumb.php', $data)->render();

        return $breadcrumb;

    }
}