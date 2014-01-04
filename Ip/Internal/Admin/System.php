<?php
namespace Ip\Internal\Admin;


class System {

    protected static $disablePanel = false;

    public function init()
    {
        ipDispatcher()->addJobListener('Ip.reasonToPreventLogin', array($this, 'jobReasonToPreventLogin'));
        ipDispatcher()->addJobListener('Cron.execute', array($this, 'onCron'));
        ipDispatcher()->addEventListener('Admin.failedLogin', array($this, 'onFailedLogin'));

        $relativePath = ipRequest()->getRelativePath();
        $request = \Ip\ServiceLocator::request();

        if (in_array($relativePath, array('admin', 'admin/', 'admin.php', 'admin.php/')) && $request->isDefaultAction()) {
            \Ip\ServiceLocator::response()->setLayout(ipFile('Ip/Internal/Admin/view/layout.php'));
            $request->setAction('Admin', 'login', \Ip\Request::CONTROLLER_TYPE_SITE);
        }

        ipDispatcher()->addEventListener('site.afterInit', array($this, 'initAdmin'));

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

    public function onFailedLogin($data)
    {
        $securityModel = SecurityModel::instance();
        $securityModel->registerFailedLogin($data['username'], $data['ip']);
    }

    public function onCron($data)
    {
        if ($data['firstTimeThisDay']) {
            $securityModel = SecurityModel::instance();
            $securityModel->cleanup();
        }
    }

    public function jobReasonToPreventLogin($data)
    {
        if (empty($data['username'])) {
            return 'Missing login data'; //in theory should never happen
        }

        $ip = ipRequest()->getServer('REMOTE_ADDR');

        $antiBruteForce = SecurityModel::instance();
        $failedLogins = $antiBruteForce->failedLoginCount($data['username'], $ip);
        if ($failedLogins > ipGetOption('Admin.allowFailedLogins', 20)) {
            return __('You have exceeded failed login attempts.', 'ipAdmin', false);
        }

    }

    public function initAdmin()
    {

        if (!self::$disablePanel && (ipIsManagementState() || !empty($_GET['aa']) ) && !empty($_SESSION['backend_session']['userId'])) {
            ipAddCss(ipFileUrl('Ip/Internal/Admin/assets/admin.css'));

            ipAddJs(ipFileUrl('Ip/Internal/Admin/assets/admin.js'));

            ipAddJsVariable('ipAdminToolbar', $this->getAdminToolbarHtml());
        }

    }


    protected function getAdminToolbarHtml()
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
            $curModTitle = $curModule; //TODOX translation
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
