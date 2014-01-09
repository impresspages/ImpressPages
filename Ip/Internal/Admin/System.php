<?php
namespace Ip\Internal\Admin;


class System {

    protected static $disablePanel = false;

    public function init()
    {
        ipDispatcher()->addJobListener('Ip.adminLoginPrevent', array($this, 'jobReasonToPreventLogin'));
        ipDispatcher()->addEventListener('Ip.cronExecute', array($this, 'onCron'));
        ipDispatcher()->addEventListener('Ip.adminLoginFailed', array($this, 'onFailedLogin'));

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


}
