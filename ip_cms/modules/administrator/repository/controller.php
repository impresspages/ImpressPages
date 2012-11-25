<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\administrator\repository;


class Controller extends \Ip\Controller{


    public function storeNewFiles()
    {
        if (!isset($_POST['files']) || !is_array($_POST['files'])) {
            $this->returnJson(array('status' => 'error', 'errorMessage' => 'Missing POST variable'));
        }

        $files = $_POST['files'];

        $newFiles = array();

        $destination = BASE_DIR.FILE_DIR;
        foreach ($files as $key => $file) {
            $newName = \Library\Php\File\Functions::genUnoccupiedName($file['renameTo'], $destination);
            copy(BASE_DIR.$file['file'], $destination.$newName);
            unlink(BASE_DIR.$file['file']);
            $newFile = array(
                'fileName' => $newName,
                'dir' => $destination,
                'file' => FILE_DIR.$newName
            );
            $newFiles[] = $newFile;
        }

        $answer = array(
            'status' => 'success',
            'files' => $newFiles
        );

        $this->returnJson($answer);
    }



}
