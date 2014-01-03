<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Wizard;


class AdminController extends \Ip\Controller{

    public function loadContent() {
        $viewData = array (
            'tip_dragWidget' => ipGetOption('Wizard.tip_dragWidget'),
            'tip_dropWidget' => ipGetOption('Wizard.tip_dropWidget'),
            'tip_changeWidgetContent' => ipGetOption('Wizard.tip_changeWidgetContent'),
            'tip_confirmWidget' => ipGetOption('Wizard.tip_confirmWidget'),
            'tip_publish' => ipGetOption('Wizard.tip_publish')
        );
        $content = ipView('view/content.php', $viewData)->render();

        $data = array(
            'status' => 'success',
            'content' => $content
        );

        // TODO JsonRpc
        return new \Ip\Response\Json($data);
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

        return new \Ip\Response\Json($data);
    }

}
