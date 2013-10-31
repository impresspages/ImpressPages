<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content\Widget;



class IpTextImage extends \Ip\Module\Content\Widget{

    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('standard', 'content_management', 'widget_text_image', 'text_image');
    }


    public function update($widgetId, $postData, $currentData) {

        $newData = $currentData;


        $newData['text'] = $postData['text'];
        $newData['title'] = $postData['title'];

        if (isset($postData['newImage']) && is_file(\Ip\Config::baseFile($postData['newImage']))) {

            //remove old image
            if (isset($currentData['imageOriginal']) && $currentData['imageOriginal']) {
                \Ip\Module\Repository\Model::unbindFile($currentData['imageOriginal'], 'standard/content_management', $widgetId);
            }
            //new original image
            \Ip\Module\Repository\Model::bindFile($postData['newImage'], 'standard/content_management', $widgetId);
            $newData['imageOriginal'] = $postData['newImage'];
            
        }

        if (isset($newData['imageOriginal']) &&  isset($postData['cropX1']) && isset($postData['cropY1']) && isset($postData['cropX2']) && isset($postData['cropY2'])) {
            $newData['cropX1'] = $postData['cropX1'];
            $newData['cropY1'] = $postData['cropY1'];
            $newData['cropX2'] = $postData['cropX2'];
            $newData['cropY2'] = $postData['cropY2'];
        }

        return $newData;
    }
    
    
    public function managementHtml($instanceId, $data, $layout) {
        global $parametersMod;
        $data['translations']['title'] = $parametersMod->getValue('standard', 'content_management', 'widget_text_image', 'title');
        return parent::managementHtml($instanceId, $data, $layout);
    }    


    public function previewHtml($instanceId, $data, $layout)
    {
        global $parametersMod;

        if (isset($data['imageOriginal'])) {
            $reflectionService = \Ip\Module\Repository\ReflectionService::instance();
            $transformBig = new \Ip\Module\Repository\Transform\None();

            $desiredName = isset($data['title']) ? $data['title'] : '';

            try {
                $data['imageBig'] = $reflectionService->getReflection($data['imageOriginal'], $desiredName, $transformBig);
            } catch (\Ip\Module\Repository\Exception $e) {
                //do nothing
            }


            if (isset($data['cropX1']) && isset($data['cropY1']) && isset($data['cropX2']) && isset($data['cropY2'])) {

                if ($data['cropY2'] - $data['cropY1'] > 0) {
                    $ratio = ($data['cropX2'] - $data['cropX1']) / ($data['cropY2'] - $data['cropY1']);
                    $requiredWidth = round($parametersMod->getValue('standard', 'content_management', 'widget_text_image', 'width'));
                    $requiredHeight = round($requiredWidth / $ratio);

                    $transformSmall = new \Ip\Module\Repository\Transform\ImageCrop(
                        $data['cropX1'],
                        $data['cropY1'],
                        $data['cropX2'],
                        $data['cropY2'],
                        $requiredWidth,
                        $requiredHeight
                    );
                    try {
                        $data['imageSmall'] = $reflectionService->getReflection($data['imageOriginal'], $data['title'], $transformSmall);
                    } catch (\Ip\Module\Repository\Exception $e) {
                        //do nothing
                    }
                }


            }

        }

        return parent::previewHtml($instanceId, $data, $layout);
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

}