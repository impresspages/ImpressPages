<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Breadcrumb;


/**
 * class to ouput current breadcrumb
 * @package ImpressPages
 */
class Model{

    /**
     * @return string HTML with links to website languages
     */
    static function generateBreadcrumb($separator, $showHome = true){
        $data = array (
            'homeUrl' => $showHome ? ipHomeUrl() : null,
            'breadcrumbElements' => ipContent()->getBreadcrumb(),
            'separator' => $separator,
        );
        
        $breadcrumb = ipView('view/breadcrumb.php', $data)->render();

        return $breadcrumb;

    }
}