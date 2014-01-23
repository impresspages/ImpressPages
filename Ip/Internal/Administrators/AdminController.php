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



        ipAddJs('Ip/Internal/Ip/assets/js/angular.js');
        ipAddJs('Ip/Internal/Administrators/assets/administratorsController.js');
        ipAddCss('Ip/Internal/Administrators/assets/administrators.css');

        foreach($administrators as &$administrator)
        {
            unset($administrator['hash']);
            unset($administrator['resetSecret']);
            unset($administrator['resetTime']);
        }

        ipAddJsVariable('ipAdministrators', $administrators);
        ipAddJsVariable('ipAdministratorsAdminId', (int)ipAdminId());

        $data = array (
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

        $errors = $form->validate($post);

        if (!empty($errors)) {
            $data = array (
                'status' => 'error',
                'errors' => $errors
            );
            return new \Ip\Response\Json($data);
        }


        $data = $form->filterValues($post);

        $username = $data['username'];
        $email = $data['email'];
        $password = $data['password'];

        Service::add($username, $email, $password);


        $data = array (
            'status' => 'ok'
        );
        return new \Ip\Response\Json($data);
    }

    public function delete()
    {
        ipRequest()->mustBePost();
        $post = ipRequest()->getPost();

        if (!isset($post['id'])) {
            throw new \Ip\Exception('Missing required parameters');
        }

        $userId = $post['id'];

        if ($userId == ipAdminId()) {
            throw new \Ip\Exception("Can't delete yourself");
        }


        Service::delete($userId);

        $data = array (
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
            $errors['username'] = __('Already taken', 'ipAdmin', FALSE);
        }

        if ($errors) {
            $data = array (
                'status' => 'error',
                'errors' => $errors
            );
            return new \Ip\Response\Json($data);
        }

        Service::update($userId, $username, $email, $password);

        $data = array (
            'status' => 'ok'
        );
        return new \Ip\Response\Json($data);
    }
}
