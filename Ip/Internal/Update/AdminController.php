<?php
/**
 * @package   ImpressPages
 */



namespace Ip\Internal\Update;


class AdminController extends \Ip\Controller{
    public function startUpdate()
    {
        $updateModel = new UpdateModel();

        try {
            $updateModel->prepareForUpdate();
            Model::runMigrations();
        } catch (UpdateException $e) {
            $data = array(
                'status' => 'error',
                'error' => $e->getMessage()
            );
            return new \Ip\Response\Json($data);
        }


        $data = array(
            'status' => 'success'
        );
        return new \Ip\Response\Json($data);
    }
}
