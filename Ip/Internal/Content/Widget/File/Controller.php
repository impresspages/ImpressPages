<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Content\Widget\File;




class Controller extends \Ip\WidgetController{

    public function getTitle() {
        return __('File', 'Ip-admin', false);
    }

    public function update($widgetId, $postData, $currentData) {

        $newData = $currentData;

        $newData['files'] = []; //we will create new files array.

        if (isset($postData['files']) && is_array($postData['files'])) {//check if files array is set
            foreach($postData['files'] as $file){
                if (isset($file['title']) && isset($file['fileName']) && isset($file['status'])){ //check if all require data present
                    switch($file['status']){
                        case 'new':
                            if (file_exists(ipFile('file/repository/' . $file['fileName']))) {

                                \Ip\Internal\Repository\Model::bindFile($file['fileName'], 'Content', $widgetId);

                                if ($file['title'] == '') {
                                    $title = basename($file['fileName']);
                                } else {
                                    $title = $file['title'];
                                }
                                $newFile = array(
                                    'fileName' => $file['fileName'],
                                    'title' => $title
                                );
                                $newData['files'][] = $newFile;
                            }
                            break;
                        case 'present'://file not changed

                            $existingFile = self::_findExistingFile($file['fileName'], $currentData['files']);
                            if ($existingFile) {
                                $newFile = [];
                                $newFile['fileName'] = $existingFile['fileName'];
                                $newFile['title'] = $file['title'];
                                $newData['files'][] = $newFile;
                            }

                            break;
                        case 'deleted':
                            $existingFile = self::_findExistingFile($file['fileName'], isset($currentData['files']) ? $currentData['files'] : null);
                            if (!$existingFile) {
                                \Ip\Internal\Repository\Model::unbindFile($existingFile['fileName'], 'Content', $widgetId);
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

    public function generateHtml($revisionId, $widgetId, $data, $skin) {
        if (empty($data['files']) || !is_array($data['files'])) {
            $data['files'] = [];
        }
        $newData = [];
        foreach($data['files'] as $file) {
            if (!isset($file['fileName'])) {
                continue;
            }

            $newFile = [];
            $newFile['url'] = ipFileUrl('file/repository/' . $file['fileName']);
            $newFile['path'] = ipFile('file/repository/' . $file['fileName']);
            $newFile['title'] = isset($file['title']) ? $file['title'] : $file['fileName'];
            $newData['files'][] = $newFile;
        }
        return parent::generateHtml($revisionId, $widgetId, $newData, $skin);
    }


    private function _findExistingFile ($fileName, $allFiles) {

        if (!is_array($allFiles)) {
            return false;
        }

        $answer = false;
        foreach ($allFiles as $file) {
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

        foreach($data['files'] as $file) {
            if (isset($file['fileName']) && $file['fileName']) {
                \Ip\Internal\Repository\Model::unbindFile($file['fileName'], 'Content', $widgetId);
            }
        };
    }



    /**
    *
    * Duplicate widget action. This function is executed after the widget is being duplicated.
     * All widget data is duplicated automatically. This method is used only in case a widget
    * needs to do some maintenance tasks on duplication.
    * @param int $oldId old widget id
    * @param int $newId duplicated widget id
    * @param array $data data that has been duplicated from old widget to the new one
    * @return array
    */
    public function duplicate($oldId, $newId, $data) {
        if (!isset($data['files']) || !is_array($data['files'])) {
            return null;
        }

        foreach($data['files'] as $file) {
            if (isset($file['fileName']) && $file['fileName']) {
                \Ip\Internal\Repository\Model::bindFile($file['fileName'], 'Content', $newId);
            }
        };
        return $data;
    }


}
