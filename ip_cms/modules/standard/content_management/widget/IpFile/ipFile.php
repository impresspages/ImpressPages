<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management\widget;

if (!defined('CMS')) exit;

require_once(BASE_DIR.MODULE_DIR.'standard/content_management/widget.php');
require_once(BASE_DIR.LIBRARY_DIR.'php/file/functions.php');


class ipFile extends \Modules\standard\content_management\Widget{



    public function prepareData($instanceId, $postData, $currentData) {
        global $parametersMod;
        $answer = '';
        
        
        $destinationDir = BASE_DIR.FILE_DIR;
        
        $newData = $currentData;
        
        $newData['files'] = array(); //we will create new files array.
        
        if (isset($postData['files']) && is_array($postData['files'])) {//check if files array is set
            foreach($postData['files'] as $filesKey => $file){
                if (isset($file['title']) && isset($file['fileName']) && isset($file['status'])){ //check if all require data present
                    switch($file['status']){
                        case 'new':
                            if (file_exists(BASE_DIR.$file['fileName'])) {
                                if (TMP_FILE_DIR.basename($file['fileName']) != $file['fileName']) {
                                    throw new \Exception("Security notice. Try to access a file (".$file['fileName'].") from a non temporary folder.");
                                }
                                $unocupiedName = \Library\Php\File\Functions::genUnocupiedName($file['fileName'], $destinationDir);
                                copy($file['fileName'], $destinationDir.$unocupiedName);
                                if ($file['title'] == '') {
                                    $title = basename($file['fileName']);
                                } else {
                                    $title = $file['title'];
                                }
                                $newFile = array(
                                    'fileName' => FILE_DIR.$unocupiedName,
                                    'title' => $title
                                );                                
                                $newData['files'][] = $newFile;
                            }
                            break;
                        case 'present':
                            if (!isset($currentData['files']) || !is_array($currentData['files'])) {
                                break; //possible hack. There is no files yet.
                            }
                            $reallyPresent = false;
                            foreach($currentData['files'] as $currentFileKey => $currentFile) {
                                if ($currentFile['fileName'] == $file['fileName']) {
                                    $reallyPresent = true;
                                }
                            }
                            if ($reallyPresent) {
                                $newFile = array();
                                $newFile['fileName'] = $currentFile['fileName'];
                                $newFile['title'] = $file['title'];
                                $newData['files'][] = $newFile;
                            }
                            
                            break;
                        case 'deleted':
                            //do nothing. File will be deleted when no links to it will be present.
                            break;
                    }
                }
            }
        }


        return $newData;
    }





}