<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Content\Widget\Image;




class Controller extends \Ip\WidgetController{


    public function getTitle() {
        return __('Image', 'ipAdmin', false);
    }


    public function update($widgetId, $postData, $currentData) {

        if (isset($postData['method'])) {
            switch($postData['method']) {
                case 'update':
                    $newData = $currentData;

                    if (isset($postData['newImage']) && is_file(ipFile('file/repository/' . $postData['newImage']))) {
                        //unbind old image
                        if (isset($currentData['imageOriginal']) && $currentData['imageOriginal']) {
                            \Ip\Internal\Repository\Model::unbindFile($currentData['imageOriginal'], 'Content', $widgetId);
                        }

                        //bind new image
                        \Ip\Internal\Repository\Model::bindFile($postData['newImage'], 'Content', $widgetId);

                        $newData['imageOriginal'] = $postData['newImage'];
                    }

                    return $newData;
                    break;
                case 'crop':
                    $newData = $currentData;
                    if (isset($postData['cropX1']) && isset($postData['cropY1']) && isset($postData['cropX2']) && isset($postData['cropY2']) && isset($postData['width'])  && isset($postData['height'])) {
                        //new small image
                        $newData['cropX1'] = $postData['cropX1'];
                        $newData['cropY1'] = $postData['cropY1'];
                        $newData['cropX2'] = $postData['cropX2'];
                        $newData['cropY2'] = $postData['cropY2'];
                        $newData['width'] = $postData['width'];
                        $newData['height'] = $postData['height'];
                    }
                    return $newData;

                    break;
            }
        }
    }

    //TODOX remove
    public function update_old($widgetId, $postData, $currentData) {

        $newData = $currentData;

        if (isset($postData['newImage']) && is_file(ipFile('file/repository/' . $postData['newImage']))) {
            //unbind old image
            if (isset($currentData['imageOriginal']) && $currentData['imageOriginal']) {
                \Ip\Internal\Repository\Model::unbindFile($currentData['imageOriginal'], 'Content', $widgetId);
            }

            //new original image
            \Ip\Internal\Repository\Model::bindFile($postData['newImage'], 'Content', $widgetId);
            $newData['imageOriginal'] = $postData['newImage'];

        }

        if (isset($postData['cropX1']) && isset($postData['cropY1']) && isset($postData['cropX2']) && isset($postData['cropY2']) && isset($postData['width'])  && isset($postData['height'])) {
            //new small image
            $newData['cropX1'] = $postData['cropX1'];
            $newData['cropY1'] = $postData['cropY1'];
            $newData['cropX2'] = $postData['cropX2'];
            $newData['cropY2'] = $postData['cropY2'];
            $newData['width'] = $postData['width'];
            $newData['height'] = $postData['height'];
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



    public function generateHtml($revisionId, $widgetId, $instanceId, $data, $skin) {
        if (isset($data['imageOriginal'])) {
            $reflectionService = \Ip\Internal\Repository\ReflectionService::instance();
            $desiredName = isset($data['title']) ? $data['title'] : 'image';

            $transformBig = new \Ip\Transform\None();
            $data['imageBig'] = $reflectionService->getReflection($data['imageOriginal'], $desiredName, $transformBig);

            if (
                isset($data['cropX1']) && isset($data['cropY1']) && isset($data['cropX2']) && isset($data['cropY2']) && isset($data['width'])  && isset($data['height'])
                && $data['cropY2'] - $data['cropY1'] > 0
            ) {
                    $transform = new \Ip\Transform\ImageCrop(
                        $data['cropX1'],
                        $data['cropY1'],
                        $data['cropX2'],
                        $data['cropY2'],
                        $data['width'],
                        $data['height']
                    );
                    $data['imageSmall'] = ipFileUrl('file/' . $reflectionService->getReflection($data['imageOriginal'], $desiredName, $transformSmall));
            } else {
                $transform = new \Ip\Transform\ImageFit(
                    ipGetOption('Content.widgetImageWidth', 800),
                    ipGetOption('Content.widgetImageHeight', 400)
                );
            }
            try {
                $data['imageSmall'] = ipFileUrl('file/' . $reflectionService->getReflection($data['imageOriginal'], $desiredName, $transform));
            } catch (\Ip\Exception\TransformException $e) {
                ipLog()->error($e->getMessage(), array('errorTrace' => $e->getTraceAsString()));
            }
        }
        return parent::generateHtml($revisionId, $widgetId, $instanceId, $data, $skin);
    }



}
