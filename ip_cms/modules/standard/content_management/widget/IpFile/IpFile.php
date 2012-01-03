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


class IpFile extends \Modules\standard\content_management\Widget{



    public function update($widgetId, $postData, $currentData) {
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
                                
                                $repositoryFilename = \Modules\administrator\repository\Model::addFile($file['fileName'], 'standard/content_management', $widgetId);
                                
                                if ($file['title'] == '') {
                                    $title = basename($file['fileName']);
                                } else {
                                    $title = $file['title'];
                                }
                                $newFile = array(
                                    'fileName' => $repositoryFilename,
                                    'title' => $title
                                );
                                $newData['files'][] = $newFile;
                            }
                            break;
                        case 'present'://file not changed

                            $existingFile = self::_findExistingFile($file['fileName'], $currentData['files']);
                            if ($existingFile) {
                                $newFile = array();
                                $newFile['fileName'] = $existingFile['fileName'];
                                $newFile['title'] = $file['title'];
                                $newData['files'][] = $newFile;
                            }

                            break;
                        case 'deleted':
                            $existingFile = self::_findExistingFile($file['fileName'], $currentData['files']);
                            if (!$existingFile) {
                                \Modules\administrator\repository\Model::unbindFile($existingFile['fileName'], 'standard/content_management', $widgetId);
                            } else {
                                //do nothing existing image not found. 
                            }
                            break;
                    }
                }
            }
        }


        return $newData;
    }

    
    private function _findExistingFile ($fileName, $allFiles) {

        if (!is_array($allFiles)) {
            return false;
        }

        $answer = false;
        foreach ($allFiles as $fileKey => $file) {
            if ($file['fileName'] == $fileName) {
                $answer = $file;
                break;
            }
        }

        return $answer;

    }    

    public function delete($widgetId, $data) {
        if (!isset($data['files']) || !is_array($data['files'])) {
            return;
        }
        
        foreach($data['files'] as $fileKey => $file) {
            if (isset($file['fileName']) && $file['fileName']) {
                \Modules\administrator\repository\Model::unbindFile($file['fileName'], 'standard/content_management', $widgetId);
            }
        };
    }



}