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

        $enableUpdate = !defined('MULTISITE_WEBSITES_DIR'); // Disable update in MultiSite installation.

        $trash = array(
            'size' => \Ip\Internal\Pages\Service::trashSize(),
        );

        if ($trash['size']) {

            $form = new \Ip\Form();

            $field = new \Ip\Form\Field\Hidden();
            $field->setName('aa');
            $field->setValue('System.emptyTrash');
            $form->addField($field);

            $submit = new \Ip\Form\Field\Submit(array(
                'value' => __('Empty', 'Ip-admin')
            ));

            $form->addField($submit);

            $trash['form_empty'] = $form;

            $form = new \Ip\Form();

            $field = new \Ip\Form\Field\Hidden();
            $field->setName('aa');
            $field->setValue('System.recoveryTrash');
            $form->addField($field);

            $submit = new \Ip\Form\Field\Submit(array(
                'value' => __('Recovery', 'Ip-admin')
            ));

            $form->addField($submit);

            $trash['form_recovery'] = $form;
        }

        $data = array(
            'notes' => $notes,
            'version' => \Ip\ServiceLocator::storage()->get('Ip', 'version'),
            'changedUrl' => $model->getOldUrl() != $model->getNewUrl(),
            'oldUrl' => $model->getOldUrl(),
            'newUrl' => $model->getNewUrl(),
            'migrationsAvailable' => \Ip\Internal\Update\Service::migrationsAvailable(),
            'migrationsUrl' => ipActionUrl(array('pa' => 'Update')),
            'trash' => $trash,
        );

        $content = ipView('view/index.php', $data)->render();

        if ($enableUpdate) {
            ipAddJs('Ip/Internal/System/assets/update.js');
        }
        ipAddJs('Ip/Internal/System/assets/migrations.js');

        return $content;
    }

    public function startUpdate()
    {
        try {
            \Ip\Internal\Update\Service::update();
        } catch (\Exception $e) {
            return new \Ip\Response\Json(array(
                'error' => $e->getMessage()
            ));

        }

        $_SESSION['Ip']['notes'][] = __('ImpressPages has been successfully updated.', 'Ip-admin');

        return new \Ip\Response\Json(array(
            'status' => 'success'
        ));
    }

    public function updateLinks()
    {
        $model = Model::instance();
        $oldUrl = $model->getOldUrl();
        $newUrl = $model->getNewUrl();

        $httpExpression = '/^((http|https):\/\/)/i';

        if ($oldUrl != $newUrl && preg_match($httpExpression, $oldUrl) && preg_match($httpExpression, $newUrl)) {
            $eventData = array(
                'oldUrl' => $oldUrl,
                'newUrl' => $newUrl
            );
            ipEvent('ipUrlChanged', $eventData);
            ipStorage()->set('Ip', 'cachedBaseUrl', $newUrl);
            $_SESSION['Ip']['notes'][] = __('Links have been successfully updated.', 'Ip-admin');
        } else {
            // In theory should never happen.
        }

        return new \Ip\Response\Redirect(ipActionUrl(array('aa' => 'System')));
        ipRequest()->mustBePost();

    }

    protected function indexUrl()
    {
        return ipConfig()->baseUrl() . '?aa=System.index';
    }

    public function getIpNotifications()
    {

        $systemInfo = Model::getIpNotifications();

        if (isset($_REQUEST['afterLogin'])) { // Request after login.
            if ($systemInfo == '') {
                $_SESSION['ipSystem']['show_system_message'] = false; // Don't display system alert at the top.
                return;
            } else {
                $md5 = \Ip\ServiceLocator::storage()->get('Ip', 'lastSystemMessageShown');
                if ($systemInfo && (!$md5 || $md5 != md5(serialize($systemInfo)))) { // We have a new message.
                    $newMessage = false;

                    foreach (json_decode($systemInfo) as $infoValue) {
                        if ($infoValue->type != 'status') {
                            $newMessage = true;
                        }
                    }

                    $_SESSION['ipSystem']['show_system_message'] = $newMessage; // Display system alert.
                } else { // This message was already seen.
                    $_SESSION['ipSystem']['show_system_message'] = false; // Don't display system alert at the top.
                    return;
                }

            }
        } else { // administrator/system tab.
            \Ip\ServiceLocator::storage()->set('Ip', 'lastSystemMessageShown', md5(serialize($systemInfo)));
            $_SESSION['ipSystem']['show_system_message'] = false; // Don't display system alert at the top.
        }

        return new \Ip\Response\Json($systemInfo);
    }

    public function emptyTrash()
    {
        \Ip\Internal\Pages\Service::emptyTrash();

        return new \Ip\Response\Json(array('redirectUrl' => ipActionUrl(array('aa' => 'System'))));
    }

    public function recoveryTrash()
    {
        \Ip\Internal\Pages\Service::recoveryTrash();

        return new \Ip\Response\Json(array('redirectUrl' => ipActionUrl(array('aa' => 'System'))));
    }

}
