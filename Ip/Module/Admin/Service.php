<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Module\Admin;

class Service
{
    /**
     * Injects admin html into old backend modules.
     *
     * @deprecated
     * @param string $html
     * @return mixed
     */
    public static function injectAdminHtml($html)
    {
        $system = new System();
        return $system->injectAdminHtml($html);
    }

    public static function isSafeMode()
    {
        return \Ip\Module\Admin\Model::isSafeMode();
    }

}