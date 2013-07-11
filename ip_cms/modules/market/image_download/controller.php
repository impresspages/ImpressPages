<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 */
namespace Modules\market\image_download;
use Ip\CoreException;

use Modules\administrator\repository\Model as RepositoryModel;

class Controller extends \Ip\Controller
{
    public function init()
    {
        if (!\Ip\Backend::loggedIn()) {
            throw new \Exception('This controller can be accessed only by administrator');
        }
    }

    public static function download() {
        global $site;

        if (empty($_GET['img_url'])) {
            throw new \Exception("img_url parameter is missing.");
        }

        // download file to tmp folder
        $img_url = $_GET['img_url'];

        if (!empty($_GET['img_filename'])) {
            // TODOX validate
            $img_filename = $_GET['img_filename'];
        } else {
            $img_url_path = parse_url($img_url, PHP_URL_PATH);
            $img_filename = basename($img_url_path);
        }

        $img_tmp_path = BASE_DIR . TMP_FILE_DIR . $img_filename;
        // TODOX add to library a method to download files safely
        // TODOX if there is no extension, detect by headers
        file_put_contents($img_tmp_path, fopen($img_url, 'r'));
        // TODOX check $http_response_header

        $destination = BASE_DIR.FILE_REPOSITORY_DIR;
        $img_real_filename = \Library\Php\File\Functions::genUnoccupiedName($img_tmp_path, $destination);
        copy($img_tmp_path, $destination . $img_real_filename);

        \Modules\administrator\repository\Model::bindFile(FILE_DIR . $img_real_filename, 'administrator/repository', 0);

        unlink($img_tmp_path);

        $browser_model = \Modules\administrator\repository\BrowserModel::instance();

        $file = $browser_model->getFile($img_real_filename);

        $site->setOutput(json_encode($file));
    }
}
