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



        ipAddCss(ipFileUrl('Ip/Internal/Administrators/assets/administrators.css'));
        ipAddJs(ipFileUrl('Ip/Internal/Administrators/assets/administrators.js'));

        $data = array (
            'administrators' => $administrators,
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
