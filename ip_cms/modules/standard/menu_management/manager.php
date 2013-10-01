<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Modules\standard\menu_management;


if (!defined('BACKEND')) {
    exit;
}


class Manager
{

    function __construct()
    {
    }


    function manage()
    {
        global $cms;

        require_once(__DIR__ . '/template.php');

        $data = array(
            'securityToken' => $cms->session->securityToken(),
            'moduleId' => $cms->curModId,
            'postURL' => $cms->generateWorkerUrl(),
            'imageDir' => BASE_URL . MODULE_DIR . 'standard/menu_management/img/'
        );

        $site = \Ip\ServiceLocator::getSite();
        $site->addCss(BASE_URL . MODULE_DIR . 'standard/menu_management/menu_management.css');
        $site->addCss(BASE_URL . MODULE_DIR . 'standard/menu_management/jquery-ui/jquery-ui.css');
        $site->addJavascript(BASE_URL . LIBRARY_DIR . 'js/default.js');
        $site->addJavascript(BASE_URL . LIBRARY_DIR . 'js/jquery/jquery.js');
        $site->addJavascript(BASE_URL . MODULE_DIR . 'standard/menu_management/jstree/jquery.cookie.js');
        $site->addJavascript(BASE_URL . MODULE_DIR . 'standard/menu_management/jstree/jquery.hotkeys.js');
        $site->addJavascript(BASE_URL . MODULE_DIR . 'standard/menu_management/jstree/jquery.jstree.js');
        $site->addJavascript(BASE_URL . MODULE_DIR . 'standard/menu_management/menu_management.js');
        $site->addJavascript(BASE_URL . MODULE_DIR . 'standard/menu_management/jquery-ui/jquery-ui.js');

        return Template::content($data);
    }

}
