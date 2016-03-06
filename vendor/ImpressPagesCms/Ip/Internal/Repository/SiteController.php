<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Repository;


/**
 * Repository controller to handle file uploads and supply
 * data about repository files to frontend.
 *
 * Files can be uploaded from frontend and backend.
 * But files from frontend can be uploaded only to
 * secured folder not accessible from the Internet
 */
class SiteController extends \Ip\Controller
{


    /**
     * Upload file to temporary folder
     */
    public function upload()
    {
        ipRequest()->mustBePost();
        $post = ipRequest()->getPost();

        if (isset($post['secureFolder']) && $post['secureFolder']) {
            //upload to secure publicly not accessible folder.
            if (!ipGetOption('Config.allowAnonymousUploads', 1)) {
                throw new \Exception('Anonymous uploads are not enabled. You can enable them by turning on "anonymous uploads" configuration value in admin.');
            } else {
                //do nothing. Anonymous uploads are allowed to secure folder
            }
            $secureFolder = true;
        } else {
            $secureFolder = false;
            $this->backendOnly();
            if (!ipAdminPermission('Repository upload')) {
                throw new \Ip\Exception('Permission denied');
            }
        }


        $uploadModel = UploadModel::instance();
        try {
            $uploadModel->handlePlupload($secureFolder);
        } catch (\Ip\Exception\Repository\Upload\ForbiddenFileExtension $e) {
            // Return JSON-RPC response
            $message = __('Forbidden file type.', 'Ip-admin');
            ipLog()->info(
                'Repository.invalidUploadedFileExtension: ' . $e->getMessage(),
                array('plugin' => 'Repository')
            );

            // TODO JSONRPC
            $answer = array(
                'jsonrpc' => '2.0',
                'error' => array(
                    'code' => $e->getCode(),
                    'message' => $message,
                    'id' => 'id'
                )
            );
            return new \Ip\Response\Json($answer);

        } catch (\Ip\Exception\Repository\Upload $e) {
            ipLog()->error('Repository.fileUploadError', array('plugin' => 'Repository', 'exception' => $e));
            $message = __('Can\'t store uploaded file. Please check server configuration.', 'Ip-admin');

            // TODO JSONRPC
            $answer = array(
                'jsonrpc' => '2.0',
                'error' => array(
                    'code' => $e->getCode(),
                    'message' => $message,
                    'id' => 'id'
                )
            );
            return new \Ip\Response\Json($answer);
        }

        $fileName = $uploadModel->getUploadedFileName();
        $file = $uploadModel->getUploadedFile();
        $targetDir = $uploadModel->getTargetDir();

        // Return JSON-RPC response
        $answerArray = array(
            "jsonrpc" => "2.0",
            "result" => null,
            "id" => "id",
            "fileName" => $fileName
        );

        if (!$secureFolder) {
            $answerArray['dir'] = $targetDir;
            $answerArray['file'] = $file;
        }

        return new \Ip\Response\Json($answerArray);

    }


    protected function backendOnly()
    {
        if (!ipAdminId()) {
            throw new \Exception('This controller can be accessed only by administrator');
        }
    }

}
