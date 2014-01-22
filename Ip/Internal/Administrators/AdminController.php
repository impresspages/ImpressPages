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
        ipAddJs('Ip/Internal/Administrators/assets/administrators.js');

        ipAddJsVariable('ipAdministrators', $administrators);

        $data = array (
            'createForm' => Helper::createForm()
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

        Model::addAdministrator($username, $email, $password);


        $data = array (
            'status' => 'ok'
        );
        return new \Ip\Response\Json($data);
    }

    public function delete()
    {



    }

    public function update()
    {

    }
}
