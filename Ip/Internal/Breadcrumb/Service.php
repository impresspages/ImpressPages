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
class Service
{


    /**
     * @param bool $showHome
     * @return string HTML with links to website languages
     */
    static function generateBreadcrumb($showHome = true)
    {
        $data = array(
            'homeUrl' => $showHome ? ipHomeUrl() : null,
            'pages' => ipContent()->getBreadcrumb()
        );

        $breadcrumb = ipView('view/breadcrumb.php', $data)->render();

        return $breadcrumb;
    }
}
