<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\System;


class AdminController extends \Ip\Controller
{


    public function index()
    {
        ipAddJs('Ip/Internal/Grid/assets/grid.js');
        ipAddJs('Ip/Internal/Grid/assets/gridInit.js');

        $notes = array();

        if (isset($_SESSION['ipSystem']['notes']) && is_array(
                $_SESSION['ipSystem']['notes']
            )
        ) {
            $notes = $_SESSION['ipSystem']['notes'];
        }

        unset($_SESSION['ipSystem']['notes']);


        $enableUpdate = !defined('MULTISITE_WEBSITES_DIR'); //disable update in MultiSite installation

        $data = array(
            'notes' => $notes,
            'version' => \Ip\ServiceLocator::storage()->get('Ip', 'version'),
        );

        $content = ipView('view/index.php', $data)->render();

        if ($enableUpdate) {
            ipAddJs('Ip/Internal/System/assets/update.js');
        }

        return $content;
    }

    //TODOXX 301
    public function clearCache()
    {
        ipRequest()->mustBePost();

        ipLog()->info('System.cacheCleared');
        $module = Model::instance();
        $cachedUrl = \Ip\ServiceLocator::storage()->get('Ip', 'cachedBaseUrl'); // get system variable
        $module->clearCache($cachedUrl);



        $_SESSION['ipSystem']['notes'][] = __('Cache was cleared.', 'ipAdmin');

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
        return ipConfig()->baseUrl() . '?aa=System.index';
    }

    public function startUpdate()
    {
        $updateModel = new UpdateModel();

        try {
            $updateModel->prepareForUpdate();
        } catch (UpdateException $e) {
            $data = array(
                'status' => 'error',
                'error' => $e->getMessage()
            );
            return new \Ip\Response\Json($data);
        }


        $data = array(
            'status' => 'success',
            'redirectUrl' => ipFileUrl('update')
        );
        return new \Ip\Response\Json($data);
    }


    public function getIpNotifications()
    {

        $systemInfo = Model::getIpNotifications();


        if (isset($_REQUEST['afterLogin'])) { // request after login.
            if ($systemInfo == '') {
                $_SESSION['ipSystem']['show_system_message'] = false; //don't display system alert at the top.
                return;
            } else {
                $md5 = \Ip\ServiceLocator::storage()->get('Ip', 'lastSystemMessageShown');
                if ($systemInfo && (!$md5 || $md5 != md5(serialize($systemInfo)))) { //we have a new message
                    $newMessage = false;

                    foreach (json_decode($systemInfo) as $infoValue) {
                        if ($infoValue->type != 'status') {
                            $newMessage = true;
                        }
                    }

                    $_SESSION['ipSystem']['show_system_message'] = $newMessage; //display system alert
                } else { //this message was already seen.
                    $_SESSION['ipSystem']['show_system_message'] = false; //don't display system alert at the top.
                    return;
                }

            }
        } else { //administrator/system tab.
            \Ip\ServiceLocator::storage()->set('Ip', 'lastSystemMessageShown', md5(serialize($systemInfo)));
            $_SESSION['ipSystem']['show_system_message'] = false; //don't display system alert at the top.
        }

        return new \Ip\Response\Json($systemInfo);
    }

}
