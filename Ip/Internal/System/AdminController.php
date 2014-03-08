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
        $model = Model::instance();
        ipAddJs('Ip/Internal/Grid/assets/grid.js');
        ipAddJs('Ip/Internal/Grid/assets/gridInit.js');

        $notes = array();

        if (isset($_SESSION['Ip']['notes']) && is_array($_SESSION['Ip']['notes'])) {
            $notes = $_SESSION['Ip']['notes'];
        }

        unset($_SESSION['Ip']['notes']);


        $enableUpdate = !defined('MULTISITE_WEBSITES_DIR'); //disable update in MultiSite installation

        $data = array(
            'notes' => $notes,
            'version' => \Ip\ServiceLocator::storage()->get('Ip', 'version'),
            'changedUrl' => $model->getOldUrl() != $model->getNewUrl(),
            'oldUrl' => $model->getOldUrl(),
            'newUrl' => $model->getNewUrl()
        );

        $content = ipView('view/index.php', $data)->render();

        if ($enableUpdate) {
            ipAddJs('Ip/Internal/System/assets/update.js');
        }

        return $content;
    }


    public function updateLinks()
    {
        $model = Model::instance();
        $oldUrl = $model->getOldUrl();
        $newUrl = $model->getNewUrl();
        if ($oldUrl != $newUrl) {
            $eventData = array(
                'oldUrl' => $oldUrl,
                'newUrl' => $newUrl
            );
            ipEvent('ipUrlChanged', $eventData);
            ipStorage()->set('Ip', 'cachedBaseUrl', $newUrl);
            $_SESSION['Ip']['notes'][] = __('Links have been successfully updated.', 'ipAdmin');
        } else {
            //in theory should never happen
        }

        return new \Ip\Response\Redirect(ipActionUrl(array('aa' => 'System')));
        ipRequest()->mustBePost();

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
