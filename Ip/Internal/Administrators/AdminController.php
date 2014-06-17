<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Administrators;


class AdminController extends \Ip\Controller
{

    public function index()
    {

        $administrators = Model::getAll();


        ipAddJs('Ip/Internal/Core/assets/js/angular.js');
        ipAddJs('Ip/Internal/Administrators/assets/administratorsController.js');

        foreach ($administrators as &$administrator) {
            unset($administrator['hash']);
            unset($administrator['resetSecret']);
            unset($administrator['resetTime']);
            $administrator['permissions'] = \Ip\Internal\AdminPermissionsModel::getUserPermissions(
                $administrator['id']
            );
        }

        ipAddJsVariable('ipAdministrators', $administrators);
        ipAddJsVariable('ipAdministratorsAdminId', (int)ipAdminId());
        ipaddJsVariable('ipAvailablePermissions', \Ip\Internal\AdminPermissionsModel::availablePermissions());
        ipaddJsVariable('ipAdministratorId', ipAdminId());
        ipaddJsVariable(
            'ipAdministratorsSuperAdminWarning',
            __('You will not be able to set other permissions for yourself!', 'Ip-admin', false)
        );

        $data = array(
            'createForm' => Helper::createForm(),
            'updateForm' => Helper::updateForm()
        );
        return ipView('view/layout.php', $data)->render();

    }

    public function add()
    {
        ipRequest()->mustBePost();
        $post = ipRequest()->getPost();

        $form = Helper::createForm();
        $form->removeSpamCheck();

        $errors = $form->validate($post);

        if (!empty($errors)) {
            $data = array(
                'status' => 'error',
                'errors' => $errors
            );
            return new \Ip\Response\Json($data);
        }


        $data = $form->filterValues($post);

        $username = $data['username'];
        $email = $data['email'];
        $password = $data['password'];

        $administratorId = Service::add($username, $email, $password);

        //set the same permissions as current administrator
        $curUserPermissions = \Ip\Internal\AdminPermissionsModel::getUserPermissions(ipAdminId());
        foreach ($curUserPermissions as $permission) {
            \Ip\Internal\AdminPermissionsModel::addPermission($permission, $administratorId);
        }


        $data = array(
            'status' => 'ok',
            'id' => $administratorId,
            'permissions' => \Ip\Internal\AdminPermissionsModel::getUserPermissions($administratorId)
        );
        return new \Ip\Response\Json($data);
    }

    public function delete()
    {
        ipRequest()->mustBePost();

        $userId = ipRequest()->getPost('id');

        if (!$userId) {
            throw new \Ip\Exception('Missing required parameters');
        }

        if ($userId == ipAdminId()) {
            throw new \Ip\Exception("Can't delete yourself");
        }

        Service::delete($userId);

        $data = array(
            'status' => 'ok'
        );
        return new \Ip\Response\Json($data);
    }

    public function update()
    {
        ipRequest()->mustBePost();
        $post = ipRequest()->getPost();

        if (!isset($post['id']) || !isset($post['username']) || !isset($post['email'])) {
            throw new \Ip\Exception('Missing required parameters');
        }


        $form = Helper::updateForm();
        $form->removeSpamCheck();
        $errors = $form->validate($post);

        $userId = $post['id'];
        $username = $post['username'];
        $email = $post['email'];
        if (isset($post['password'])) {
            $password = $post['password'];
        } else {
            $password = null;
        }


        $existingUser = Service::getByUsername($username);
        if ($existingUser && $existingUser['id'] != $userId) {
            $errors['username'] = __('Already taken', 'Ip-admin', false);
        }

        if ($errors) {
            $data = array(
                'status' => 'error',
                'errors' => $errors
            );
            return new \Ip\Response\Json($data);
        }

        Service::update($userId, $username, $email, $password);

        $data = array(
            'status' => 'ok'
        );
        return new \Ip\Response\Json($data);
    }

    public function setAdminPermission()
    {
        ipRequest()->mustBePost();
        $post = ipRequest()->getPost();

        if (!isset($post['permission']) || !isset($post['value']) || !isset($post['adminId'])) {
            throw new \Ip\Exception('Missing required parameters');
        }

        $permission = $post['permission'];
        $value = $post['value'];
        $adminId = $post['adminId'];

        if ($value) {
            \Ip\Internal\AdminPermissionsModel::addPermission($permission, $adminId);
        } else {
            \Ip\Internal\AdminPermissionsModel::removePermission($permission, $adminId);
        }

        $data = array(
            'status' => 'ok'
        );
        return new \Ip\Response\Json($data);

    }
}
