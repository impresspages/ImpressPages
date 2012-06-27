<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\administrator\wizard;

if (!defined('CMS')) exit;

class Controller extends \Ip\Controller{

    public function loadContent() {
        global $site;
        global $parametersMod;
        $viewData = array (
            'tip1' => $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_1'),
            'tip2' => $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_2'),
            'tip3' => $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_3'),
            'tip4' => $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_4'),
            'tip5' => $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_5'),
            'tip6' => $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_6')
        );
        $content = \Ip\View::create('view/content.php', $viewData)->render();

        $data = array(
            'status' => 'success',
            'content' => $content
        );
        $answer = json_encode($data);
        $site->setOutput($answer);
    }

    public function closeWizard() {
        global $site;
        require_once(__DIR__.'/model.php');

        $isDisabled = Model::disableWizard();
        $data = array(
            'status' => $isDisabled ? 'success' : 'error'
        );

        $answer = json_encode($data);
        $site->setOutput($answer);
    }

    public function closeWizardTip() {
        require_once(__DIR__.'/model.php');

        $tipId = Model::disableWizardTip($_POST['id']);
        $data = array(
            'status' => $tipId ? 'success' : 'error',
            'tipId' => $tipId
        );

        $this->returnJson($data);
    }

}
