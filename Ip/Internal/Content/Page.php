<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Content;


/**
 * Page class.<br />
 *
 * @package ImpressPages
 */

class Page extends \Ip\Page
{
    protected $dynamicModules;
    protected $linkIgnoreRedirect;

    public function getLink($ignoreRedirect = false)
    {
        if (ipIsManagementState()) {
            $ignoreRedirect = true;
        }

        return parent::getLink();
    }
}
