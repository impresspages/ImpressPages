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
        $viewData = array (
            'step1' => true
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

}
