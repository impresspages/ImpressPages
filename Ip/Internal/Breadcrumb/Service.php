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
class Service{

    /**
     * @return string HTML with links to website languages
     */
    static function generateBreadcrumb($separator, $showHome = true){
        $data = array (
            'homeUrl' => $showHome ? ipHomeUrl() : null,
            'pages' => ipContent()->getBreadcrumb(),
            'separator' => $separator,
        );

        $breadcrumb = ipView('view/breadcrumb.php', $data)->render();

        return $breadcrumb;

    }
}
