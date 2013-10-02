<?php
namespace Ip\Module\Admin;


class Controller extends \Ip\Controller{


    public function login()
    {
        $request = \Ip\ServiceLocator::getRequest();
        if ($request->isPost() && $request->getPost('login') && $request->getPost('password')) {
            $validateForm = $this->getLoginForm();
            $errors = $validateForm->validate($request->getPost());
            if (empty($errors) && Model::instance()->login($request->getPost('login'), $request->getPost('password'))) {
                $this->redirect(BASE_URL.'?cms_action=manage');
            } else {
                $this->redirect(BASE_URL.'admin');
            }
        }


        $site = \Ip\ServiceLocator::getSite();

        global $cms;
        $cms = new OldCmsInterface();

        $variables = array(
            'loginUrl' => $cms->generateActionUrl('login'),
            'loginForm' => $this->getLoginForm()
        );

        $view = \Ip\View::create('View/login.php', $variables);
        $site->setOutput($view);
    }

    protected function getLoginForm()
    {
        $parametersMod = \Ip\ServiceLocator::getParametersMod();
        //create form object
        $form = new \Modules\developer\form\Form();

        $form->setAction(BASE_URL);

        //add text field to form object
        $field = new \Modules\developer\form\Field\Hidden(
            array(
                'name' => 'a',
                'defaultValue' => 'Admin.login', //html "name" attribute
            ));
        $form->addField($field);


        //add text field to form object
        $field = new \Modules\developer\form\Field\Text(
            array(
                'name' => 'login', //html "name" attribute
                'label' => $parametersMod->getValue('standard','configuration','system_translations','login_name')
            ));
        $form->addField($field);

        //add text field to form object
        $field = new \Modules\developer\form\Field\Password(
            array(
                'name' => 'password', //html "name" attribute
                'label' => $parametersMod->getValue('standard','configuration','system_translations','login_password')
            ));
        $form->addField($field);


        //add text field to form object
        $field = new \Modules\developer\form\Field\Submit(
            array(
                'defaultValue' => $parametersMod->getValue('standard','configuration','system_translations','login_login')
            ));
        $form->addField($field);



        return $form;
    }
}