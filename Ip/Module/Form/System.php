<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Module\Form;


class System
{
    public function init()
    {
        ipAddJavascript(ipGetConfig()->coreModuleUrl('Assets/assets/js/jquery.js'));
        ipAddJavascript(ipGetConfig()->coreModuleUrl('Form/assets/form.js'));
    }
}