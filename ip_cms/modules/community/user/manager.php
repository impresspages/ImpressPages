<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\community\user;


if (!defined('BACKEND')) exit;

require_once(BASE_DIR.MODULE_DIR.'developer/std_mod/std_mod.php');
global $site;
$site->requireConfig("community/user/config.php");



class Manager {
    var $standard_module;
    function __construct() {
        global $parametersMod;

        global $db;

        /* user */

        $elements = array();

        $element = new \Modules\developer\std_mod\elementText(
        array(
                    'title' => $parametersMod->getValue('community', 'user', 'admin_translations', 'login'),
                    'dbField' => 'login',
                    'showOnList' => true,
                    'searchable' => true,
                    'required' => true
        )
        );
        $elements[] = $element;


        $element = new \Modules\developer\std_mod\elementText(
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



        $element = new \Modules\developer\std_mod\elementPswd(
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

        $element = new \Modules\developer\std_mod\elementBool(
        array(
                    'title' => $parametersMod->getValue('community', 'user', 'admin_translations', 'verified'),
                    'dbField' => 'verified',
                    'showOnList' => true,
                    'searchable' => true,
                    'required' => true
        ));
        $elements[] = $element;


        $element = new \Modules\developer\std_mod\elementText(
        array(
                    'title' => $parametersMod->getValue('community', 'user', 'admin_translations', 'created_on'),
                    'dbField' => 'created_on',
                    'showOnList' => true,
                    'searchable' => true,
                    'disabledOnUpdate' => true,
                    'required' => true)
        );
        $elements[] = $element;

        $element = new \Modules\developer\std_mod\elementText(
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

        $element = new \Modules\developer\std_mod\elementText(
        array(
                    'title' => $parametersMod->getValue('community', 'user', 'admin_translations', 'last_login'),
                    'dbField' => 'last_login',
                    'showOnList' => true,
                    'searchable' => true,
                    'disabledOnUpdate' => true,
                    'required' => true
        ));
        $elements[] = $element;


        $area0 = new \Modules\developer\std_mod\Area();
        $area0->dbTable = "m_community_user";
        $area0->title = $parametersMod->getValue('community', 'user', 'admin_translations', 'user');
        $area0->dbPrimaryKey = "id";
        $area0->elements = $elements;
        $area0->sortable = false;
        $area0->searchable = true;
        $area0->orderBy = "id";
        $area0->orderDirection = "desc";
        $area0->allowInsert = false;



        $this->standard_module = new \Modules\developer\std_mod\StandardModule($area0);
    }
    function manage() {
        
        $answer = '';
        
        $answer .= $this->standard_module->manage(); 
        return $answer;

    }


}
