<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content\Widget\IpImage;




class Controller extends \Ip\WidgetController{


    public function getTitle() {
        return __('Image', 'ipAdmin');
    }

    
    public function update($widgetId, $postData, $currentData) {

        $newData = $currentData;
        $newData['imageWindowWidth'] = $postData['imageWindowWidth'];

        if (isset($postData['newImage']) && is_file(ipConfig()->baseFile($postData['newImage']))) {
            //remove old image
            if (isset($currentData['imageOriginal']) && $currentData['imageOriginal']) {
                \Ip\Module\Repository\Model::unbindFile($currentData['imageOriginal'], 'standard/content_management', $widgetId);
            }
            
            //new original image
            \Ip\Module\Repository\Model::bindFile($postData['newImage'], 'standard/content_management', $widgetId);
            $newData['imageOriginal'] = $postData['newImage'];
            
        }

        if (isset($postData['cropX1']) && isset($postData['cropY1']) && isset($postData['cropX2']) && isset($postData['cropY2']) && isset($postData['scale']) && isset($postData['maxWidth'])) {
            //new small image
            $newData['cropX1'] = $postData['cropX1'];
            $newData['cropY1'] = $postData['cropY1'];
            $newData['cropX2'] = $postData['cropX2'];
            $newData['cropY2'] = $postData['cropY2'];
            $newData['scale'] = $postData['scale'];
            $newData['maxWidth'] = $postData['maxWidth'];
            
        }



        if (isset($postData['title'])) {
            $newData['title'] = $postData['title'];
        }

        return $newData;
    }

    public function delete($widgetId, $data) {
        self::_deleteImage($data, $widgetId);
    }
    
    private function _deleteImage($data, $widgetId) {
        if (!is_array($data)) {
            return;
        }
        if (isset($data['imageOriginal']) && $data['imageOriginal']) {
            \Ip\Module\Repository\Model::unbindFile($data['imageOriginal'], 'standard/content_management', $widgetId);
        }
    }
    
    
    /**
    *
    * Duplicate widget action. This function is executed after the widget is being duplicated.
    * All widget data is duplicated automatically. This method is used only in case a widget
    * needs to do some maintenance tasks on duplication.
    * @param int $oldId old widget id
    * @param int $newId duplicated widget id
    * @param array $data data that has been duplicated from old widget to the new one
    */
    public function duplicate($oldId, $newId, $data) {
        if (!is_array($data)) {
            return;
        }
        if (isset($data['imageOriginal']) && $data['imageOriginal']) {
            \Ip\Module\Repository\Model::bindFile($data['imageOriginal'], 'standard/content_management', $newId);
        }
    }



    public function previewHtml($instanceId, $data, $layout) {

        if (isset($data['imageOriginal'])) {
            $reflectionService = \Ip\Module\Repository\ReflectionService::instance();
            $desiredName = isset($data['title']) ? $data['title'] : '';

            $transformBig = new \Ip\Module\Repository\Transform\None();
            $data['imageBig'] = $reflectionService->getReflection($data['imageOriginal'], $desiredName, $transformBig);

            if (isset($data['cropX1']) && isset($data['cropY1']) && isset($data['cropX2']) && isset($data['cropY2']) && isset($data['maxWidth']) && isset($data['scale'])) {
                if ($data['cropY2'] - $data['cropY1'] > 0){
                    $ratio = ($data['cropX2'] - $data['cropX1']) / ($data['cropY2'] - $data['cropY1']);
                    $requiredWidth = round($data['maxWidth'] * $data['scale']);
                    $requiredHeight = round($requiredWidth / $ratio);


                    $transformSmall = new \Ip\Module\Repository\Transform\ImageCrop(
                        $data['cropX1'],
                        $data['cropY1'],
                        $data['cropX2'],
                        $data['cropY2'],
                        $requiredWidth,
                        $requiredHeight
                    );
                    $data['imageSmall'] = $reflectionService->getReflection($data['imageOriginal'], $data['title'], $transformSmall);
                }
            }
        }
        return parent::previewHtml($instanceId, $data, $layout);
    }



}