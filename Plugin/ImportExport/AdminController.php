<?php
namespace Plugin\ImportExport;


use Ip\Form\Exception;

class AdminController extends \Ip\Controller
{

    public function index()
    {

        $form = Model::getForm();

        $data = array (
            'form' => $form
        );

        $view = ipView('view/index.php', $data);

        ipAddJs(ipFileUrl('Plugin/ImportExport/assets/importExport.js'));

        return $view->render();
    }

    public function import()
    {
        $form = Model::getForm();

        $fileField = $form->getField('siteFile');
        $files = $fileField->getFiles($_POST, $fileField->getName());

        $service = New Service();


        foreach ($files as $file){
            $service->startImport($file);
        }


        $response['log'] =   $service->getImportLog();
        $response['status'] =   'success';
        return new \Ip\Response\Json($response);
    }
}