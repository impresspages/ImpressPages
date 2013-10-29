<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\User;



class AdminController {


    function index() {
        $parametersMod = \Ip\ServiceLocator::getParametersMod();

        $elements = array();

        $element = new \Ip\Lib\StdMod\Element\Number(
            array(
                'title' => 'Id',
                'dbField' => 'id',
                'showOnList' => true,
                'searchable' => true,
                'required' => true
            )
        );
        $elements[] = $element;

        $element = new \Ip\Lib\StdMod\Element\Text(
            array(
                'title' => $parametersMod->getValue('community', 'user', 'admin_translations', 'login'),
                'dbField' => 'login',
                'showOnList' => true,
                'searchable' => true,
                'required' => true
            )
        );
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Text(
            array(
                'title' => $parametersMod->getValue('community', 'user', 'admin_translations', 'email'),
                'dbField' => 'email',
                'showOnList' => true,
                'searchable' => true,
                'required' => true,
                'regExpression' => $parametersMod->getValue('developer', 'std_mod','parameters','email_reg_expression'),
                'regExpressionError' => $parametersMod->getValue('community', 'user','admin_translations','error_email')
            ));
        $elements[] = $element;



        $element = new \Ip\Lib\StdMod\Element\Pswd(
            array(
                'title' => $parametersMod->getValue('community', 'user', 'admin_translations', 'password'),
                'dbField' => 'password',
                'showOnList' => true,
                'searchable' => true,
                'required' => true,
                'useHash' => $parametersMod->getValue('community', 'user', 'options', 'encrypt_passwords'),
                'hashSalt' => Config::$hashSalt
            ));
        $elements[] = $element;

        $element = new \Ip\Lib\StdMod\Element\Bool(
            array(
                'title' => $parametersMod->getValue('community', 'user', 'admin_translations', 'verified'),
                'dbField' => 'verified',
                'showOnList' => true,
                'searchable' => true,
                'required' => true
            ));
        $elements[] = $element;


        $element = new \Ip\Lib\StdMod\Element\Text(
            array(
                'title' => $parametersMod->getValue('community', 'user', 'admin_translations', 'created_on'),
                'dbField' => 'created_on',
                'showOnList' => true,
                'searchable' => true,
                'disabledOnUpdate' => true,
                'required' => true)
        );
        $elements[] = $element;

        $element = new \Ip\Lib\StdMod\Element\Text(
            array(
                'title' => $parametersMod->getValue('community', 'user', 'admin_translations', 'warned_on'),
                'dbField' => 'warned_on',
                'showOnList' => true,
                'searchable' => true,
                'disabledOnUpdate' => true,
                'required' => true
            )
        );

        $elements[] = $element;

        $element = new \Ip\Lib\StdMod\Element\Text(
            array(
                'title' => $parametersMod->getValue('community', 'user', 'admin_translations', 'last_login'),
                'dbField' => 'last_login',
                'showOnList' => true,
                'searchable' => true,
                'disabledOnUpdate' => true,
                'required' => true
            ));
        $elements[] = $element;


        $area0 = new \Ip\Lib\StdMod\Area();
        $area0->dbTable = "m_community_user";
        $area0->title = $parametersMod->getValue('community', 'user', 'admin_translations', 'user');
        $area0->dbPrimaryKey = "id";
        $area0->elements = $elements;
        $area0->sortable = false;
        $area0->searchable = true;
        $area0->orderBy = "id";
        $area0->orderDirection = "desc";
        $area0->allowInsert = false;



        $stdMod = new \Ip\Lib\StdMod\StandardModule($area0, 'User.index');


        $answer = $stdMod->manage();
        return $answer;

    }





}
