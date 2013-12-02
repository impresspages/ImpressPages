<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\System;



class AdminController extends \Ip\Controller{


    public function index()
    {

        $notes = array();

        if (isset($_SESSION['modules']['administrator']['system']['notes']) && is_array($_SESSION['modules']['administrator']['system']['notes'])) {
            $notes = $_SESSION['modules']['administrator']['system']['notes'];
        }

        unset($_SESSION['modules']['administrator']['system']['notes']);


        $enableUpdate = !defined('MULTISITE_WEBSITES_DIR'); //disable update in MultiSite installation

        $data = array(
            'notes' => $notes,
            'version' => \Ip\Internal\DbSystem::getSystemVariable('version')
        );

        $content = \Ip\View::create('view/index.php', $data)->render();

        ipAddJavascript(ipUrl('Ip/Module/Assets/assets/js/default.js'));
        ipAddJavascript(ipUrl('Ip/Module/Assets/assets/js/jquery.js'));
        ipAddJavascript(ipUrl('Ip/Module/Assets/assets/js/default.js'));
        ipAddJavascript(ipUrl('Ip/Module/Assets/assets/js/jquery.js'));

        ipAddCss(ipUrl('Ip/Module/Admin/assets/backend/ip_admin.css'));

        if ($enableUpdate){
            ipAddJavascript(ipUrl('Ip/Module/System/assets/update.js'));
        }
        ipAddJavascript(ipUrl('Ip/Module/System/assets/clearCache.js'));

        return $content;
    }

    public function clearCache()
    {
        ipRequest()->mustBePost();

        ipLog()->info('System.cacheCleared');
        $module = new Module;
        $cachedUrl = \Ip\Internal\DbSystem::getSystemVariable('cached_base_url'); // get system variable
        $module->clearCache($cachedUrl);
        $success = $module->updateRobotsTxt($cachedUrl);

        if (!$success) {
            $_SESSION['modules']['administrator']['system']['notes'][] = __('robots.txt file needs to be updated manually.', 'ipAdmin');
        }

        $_SESSION['modules']['administrator']['system']['notes'][] = __('Cache was cleared.', 'ipAdmin');

        // TODO JSONRPC
        $answer = array(
            'jsonrpc' => '2.0',
            'result' => array(
                'redirectUrl' => $this->indexUrl()
            ),
            'id' => null,
        );

        return new \Ip\Response\Json($answer);
    }

    protected function indexUrl()
    {
        return str_replace('&amp;', '&', \Ip\Internal\Deprecated\Url::generate(null, null, null, array('aa' => 'System.index')));
    }

    public function startUpdate() {
        $updateModel = new UpdateModel();

        try {
            $updateModel->prepareForUpdate();
        } catch (UpdateException $e) {
            $data = array (
                'status' => 'error',
                'error' => $e->getMessage()
            );
            return new \Ip\Response\Json($data);
        }


        $data = array (
            'status' => 'success',
            'redirectUrl' => ipConfig()->baseUrl('update')
        );
        return new \Ip\Response\Json($data);
    }


    public function getSystemInfo()
    {

        $module = new Module();
        $systemInfo = $module->getSystemInfo();


        if(isset($_REQUEST['afterLogin'])) { // request after login.
            if($systemInfo == '') {
                $_SESSION['modules']['administrator']['system']['show_system_message'] = false; //don't display system alert at the top.
                return;
            } else {
                $md5 = \Ip\Internal\DbSystem::getSystemVariable('last_system_message_shown');
                if($systemInfo && (!$md5 || $md5 != md5($systemInfo)) ) { //we have a new message
                    $newMessage = false;

                    foreach(json_decode($systemInfo) as $infoValue) {
                        if($infoValue->type != 'status') {
                            $newMessage = true;
                        }
                    }

                    $_SESSION['modules']['administrator']['system']['show_system_message'] = $newMessage; //display system alert
                } else { //this message was already seen.
                    $_SESSION['modules']['administrator']['system']['show_system_message'] = false; //don't display system alert at the top.
                    return;
                }

            }
        } else { //administrator/system tab.
            \Ip\Internal\DbSystem::setSystemVariable('last_system_message_shown', md5($systemInfo));
            $_SESSION['modules']['administrator']['system']['show_system_message'] = false; //don't display system alert at the top.
        }


        return new \Ip\Response($systemInfo);
    }

}
