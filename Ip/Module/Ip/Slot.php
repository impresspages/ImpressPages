<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Ip;


/**
 * class to ouput current breadcrumb
 * @package ImpressPages
 */
class Slot {
    public static function breadcrumb($params)
    {
        $showHome = isset($params['showHome']) ? $params['showHome'] : null;
        return \Ip\Module\Breadcrumb\Model::generateBreadcrumb(' &rsaquo; ', $showHome);
    }
}