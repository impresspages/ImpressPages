<?php


namespace Ip\Internal\Admin;


class Event
{
    public static function ipInitFinished()
    {

        if (!self::$disablePanel && (ipIsManagementState() || !empty($_GET['aa']) ) && !empty($_SESSION['backend_session']['userId'])) {
            ipAddCss(ipFileUrl('Ip/Internal/Admin/assets/admin.css'));

            ipAddJs(ipFileUrl('Ip/Internal/Admin/assets/admin.js'));

            ipAddJsVariable('ipAdminToolbar', static::getAdminToolbarHtml());
        }
    }

    protected static function getAdminToolbarHtml()
    {
        $requestData = \Ip\ServiceLocator::request()->getRequest();
        $curModTitle = '';
        $curModUrl = '';
        $helpUrl = 'http://www.impresspages.org/help2';

        if (!empty($requestData['aa'])) {
            $parts = explode('.', $requestData['aa']);
            $curModule = $parts[0];
        } elseif (ipIsManagementState()) {
            $curModule = "Content";
        }

        if (isset($curModule) && $curModule) {
            $helpUrl = 'http://www.impresspages.org/help2/' . $curModule;
            $curModTitle = __($curModule, 'ipAdmin', false);
            $curModUrl = ipActionUrl(array('aa' => $curModule . '.index'));
        }

        $data = array(
            'menuItems' => Model::instance()->getAdminMenuItems(),
            'curModTitle' => $curModTitle,
            'curModUrl' => $curModUrl,
            'helpUrl' => $helpUrl
        );
        $html = ipView('view/toolbar.php', $data)->render();
        return $html;
    }


} 