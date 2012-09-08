<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\administrator\wizard;


class Controller extends \Ip\Controller{

    public function loadContent() {
        global $site;
        global $parametersMod;
        $viewData = array (
            'tip_dragWidget' => $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_dragWidget'),
            'tip_dropWidget' => $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_dropWidget'),
            'tip_changeWidgetContent' => $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_changeWidgetContent'),
            'tip_confirmWidget' => $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_confirmWidget'),
            'tip_publish' => $parametersMod->getValue('administrator', 'wizard', 'options', 'tip_publish')
        );
        $content = \Ip\View::create('view/content.php', $viewData)->render();

        $data = array(
            'status' => 'success',
            'content' => $content
        );
        $answer = json_encode($data);
        $site->setOutput($answer);
    }


    public function closeWizardTip() {
        if (!isset($_POST['id'])) {
            trigger_error("Required parameter missing");
        }
        $tipId = $_POST['id'];

        $model = new Model();

        if (!in_array($tipId, $model->getTipIds())) {
            trigger_error("Unknown tip id");
        }

        $model->disableWizardTip($tipId);

        $data = array(
            'status' => $tipId ? 'success' : 'error',
            'tipId' => $tipId
        );

        $this->returnJson($data);
    }

}
