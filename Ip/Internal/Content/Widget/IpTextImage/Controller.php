<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Content\Widget\IpTextImage;



class Controller extends \Ip\WidgetController{

    public function getTitle() {
        return __('Text with image', 'ipAdmin', false);
    }


    public function update($widgetId, $postData, $currentData) {

        $newData = $currentData;


        $newData['text'] = $postData['text'];
        $newData['title'] = $postData['title'];

        if (isset($postData['newImage']) && is_file(ipFile('file/repository/' . $postData['newImage']))) {

            //remove old image
            if (isset($currentData['imageOriginal']) && $currentData['imageOriginal']) {
                \Ip\Internal\Repository\Model::unbindFile($currentData['imageOriginal'], 'Content', $widgetId);
            }
            //new original image
            \Ip\Internal\Repository\Model::bindFile($postData['newImage'], 'Content', $widgetId);
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
    
    



    public function generateHtml($revisionId, $widgetId, $instanceId, $data, $layout)
    {

        if (isset($data['imageOriginal'])) {
            $reflectionService = \Ip\Internal\Repository\ReflectionService::instance();
            $transformBig = new \Ip\Internal\Repository\Transform\None();

            $desiredName = isset($data['title']) ? $data['title'] : '';

            try {
                $data['imageBig'] = $reflectionService->getReflection($data['imageOriginal'], $desiredName, $transformBig);
            } catch (\Ip\Internal\Repository\Exception $e) {
                //do nothing
            }


            if (isset($data['cropX1']) && isset($data['cropY1']) && isset($data['cropX2']) && isset($data['cropY2'])) {

                if ($data['cropY2'] - $data['cropY1'] > 0) {
                    $ratio = ($data['cropX2'] - $data['cropX1']) / ($data['cropY2'] - $data['cropY1']);
                    $requiredWidth = round(ipGetOption('Content.widgetTextImageWidth'));
                    $requiredHeight = round($requiredWidth / $ratio);

                    $transformSmall = new \Ip\Internal\Repository\Transform\ImageCrop(
                        $data['cropX1'],
                        $data['cropY1'],
                        $data['cropX2'],
                        $data['cropY2'],
                        $requiredWidth,
                        $requiredHeight
                    );
                    try {
                        $data['imageSmall'] = ipFileUrl('file/' . $reflectionService->getReflection($data['imageOriginal'], $data['title'], $transformSmall));
                    } catch (\Ip\Internal\Repository\Exception $e) {
                        //do nothing
                    }
                }


            }

        }

        return parent::generateHtml($revisionId, $widgetId, $instanceId, $data, $layout);
    }

    public function delete($widgetId, $data) {
        self::_deleteImage($data, $widgetId);
    }
    
    private function _deleteImage($data, $widgetId) {
        if (!is_array($data)) {
            return;
        }
        if (isset($data['imageOriginal']) && $data['imageOriginal']) {
            \Ip\Internal\Repository\Model::unbindFile($data['imageOriginal'], 'Content', $widgetId);
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
            \Ip\Internal\Repository\Model::bindFile($data['imageOriginal'], 'Content', $newId);
        }
    }

}