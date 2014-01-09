<?php


namespace Ip\Internal\Admin;


class Event
{
    public static function ipInitFinished()
    {
        // Show admin toolbar if admin is logged in:
        if ((ipIsManagementState() || !empty($_GET['aa']) ) && !empty($_SESSION['backend_session']['userId'])) {
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

    public function init()
    {
        $relativePath = ipRequest()->getRelativePath();
        $request = \Ip\ServiceLocator::request();

        if (in_array($relativePath, array('admin', 'admin/', 'admin.php', 'admin.php/')) && $request->isDefaultAction()) {
            \Ip\ServiceLocator::response()->setLayout(ipFile('Ip/Internal/Admin/view/layout.php'));
            $request->setAction('Admin', 'login', \Ip\Request::CONTROLLER_TYPE_SITE);
        }

        if (ipIsManagementState() || !empty($_GET['aa']) || !empty($_GET['admin'])) {
            $sessionLifetime = ini_get('session.gc_maxlifetime');
            if (!$sessionLifetime) {
                $sessionLifetime = 120;
            }
            ipAddJsVariable('ipAdminSessionRefresh', $sessionLifetime - 10);
        }

        $getVariables = ipRequest()->getRequest();
        if (isset($getVariables['safemode'])) {
            $getVariables['safeMode'] = $getVariables['safemode'];
        }
        if (isset($getVariables['safeMode']) && \Ip\Internal\Admin\Backend::userId()) {
            Model::setSafeMode($getVariables['safeMode']);
        }
    }

    public static function ipAdminLoginFailed($data)
    {
        $securityModel = SecurityModel::instance();
        $securityModel->registerFailedLogin($data['username'], $data['ip']);
    }

    public static function ipCronExecute($data)
    {
        if ($data['firstTimeThisDay']) {
            $securityModel = SecurityModel::instance();
            $securityModel->cleanup();
        }
    }

} 