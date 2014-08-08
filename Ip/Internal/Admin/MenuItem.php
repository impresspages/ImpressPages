<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Internal\Admin;


class MenuItem extends \Ip\Menu\Item
{
    /**
     * @var string
     */
    protected $icon;

    public function getIcon()
    {
        return $this->icon;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }
}
