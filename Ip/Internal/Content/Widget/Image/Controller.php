<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Content\Widget\Image;




class Controller extends \Ip\WidgetController{


    public function getTitle() {
        return __('Image', 'Ip-admin', false);
    }


    public function update($widgetId, $postData, $currentData) {

        if (isset($postData['method'])) {
            switch($postData['method']) {

                case 'resize':
                    $newData = $currentData;
                    if (!isset($postData['width']) || !$postData['height']) {
                        ipLog()->error("Image widget resize missing required parameter", $postData);
                        throw new \Ip\Exception("Missing required data");
                    }
                    $newData['width'] = $postData['width'];
                    $newData['height'] = $postData['height'];
                    return $newData;
                    break;
                case 'autosize':
                    unset($currentData['width']);
                    unset($currentData['height']);
                    return $currentData;
                    break;
                case 'update':
                    $newData = $currentData;

                    if (isset($postData['fileName']) && is_file(ipFile('file/repository/' . $postData['fileName']))) {
                        //unbind old image
                        if (isset($currentData['imageOriginal']) && $currentData['imageOriginal']) {
                            \Ip\Internal\Repository\Model::unbindFile($currentData['imageOriginal'], 'Content', $widgetId);
                        }

                        //bind new image
                        \Ip\Internal\Repository\Model::bindFile($postData['fileName'], 'Content', $widgetId);

                        $newData['imageOriginal'] = $postData['fileName'];
                    }

                    if (isset($postData['cropX1']) && isset($postData['cropY1']) && isset($postData['cropX2']) && isset($postData['cropY2'])) {
                        //new small image
                        $newData['cropX1'] = $postData['cropX1'];
                        $newData['cropY1'] = $postData['cropY1'];
                        $newData['cropX2'] = $postData['cropX2'];
                        $newData['cropY2'] = $postData['cropY2'];
                    }
                    return $newData;

                    break;
                case 'setLink':
                    if (isset($postData['type'])) {
                        $currentData['type'] = $postData['type'];
                    }
                    if (isset($postData['url'])) {
                        $currentData['url'] = $postData['url'];
                    }
                    if (isset($postData['blank'])) {
                        $currentData['blank'] = $postData['blank'];
                    }
                    return $currentData;

                    break;
                case 'saveSettings':
                    if (isset($postData['title'])) {
                        $currentData['title'] = $postData['title'];
                    }
                    if (isset($postData['description'])) {
                        $currentData['description'] = $postData['description'];
                    }
                    return $currentData;

                    break;
            }
        }
        return $currentData;
    }

    protected function updateImage($curData)
    {

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
            return $data;
        }
        if (isset($data['imageOriginal']) && $data['imageOriginal']) {
            \Ip\Internal\Repository\Model::bindFile($data['imageOriginal'], 'Content', $newId);
        }
        return $data;
    }



    public function generateHtml($revisionId, $widgetId, $data, $skin) {
        if (isset($data['imageOriginal'])) {
            $desiredName = isset($data['title']) ? $data['title'] : null;

            $transformBig = array (
                'type' => 'copy'
            );

            try {
                $data['imageBig'] = ipFileUrl(ipReflection($data['imageOriginal'], $transformBig, $desiredName));
            } catch (\Ip\Exception\TransformException $e) {
                $data['imageBig'] = '';
                ipLog()->error($e->getMessage(), array('errorTrace' => $e->getTraceAsString()));
            }

            if (!empty($data['url']) && !preg_match('/^((http|https):\/\/)/i', $data['url'])) {
                $data['url'] = 'http://' . $data['url'];
            }


            if (
                isset($data['cropX1']) && isset($data['cropY1']) && isset($data['cropX2']) && isset($data['cropY2'])
                && $data['cropY2'] - $data['cropY1'] > 0
            ) {
                if (!empty($data['width'])) {
                    $width = $data['width'];
                } else {
                    $width = ipGetOption('Content.widgetImageWidth', 1200);
                }
                if ($width <= 0) {
                    $width = 1200;
                }


                //calc height
                $ratio = ($data['cropX2'] - $data['cropX1']) / ($data['cropY2'] - $data['cropY1']);
                if ($ratio == 0) {
                    $ratio = 1;
                }
                $height = round($width / $ratio);

                $transform = array(
                    'type' => 'crop',
                    'x1' => $data['cropX1'],
                    'y1' => $data['cropY1'],
                    'x2' => $data['cropX2'],
                    'y2' => $data['cropY2'],
                    'width' => $width,
                    'height' => $height
                );
                try {
                    $data['imageSmall'] = ipFileUrl(ipReflection($data['imageOriginal'], $transform, $desiredName));
                } catch (\Ip\Exception\TransformException $e) {
                    $data['imageSmall'] = '';
                    ipLog()->error($e->getMessage(), array('errorTrace' => $e->getTraceAsString()));
                }
            } else {
                if (!empty($data['width'])) {
                    $width = $data['width'];
                } else {
                    $width = ipGetOption('Content.widgetImageWidth', 1200);
                }

                if (!empty($data['height'])) {
                    $height = $data['height'];
                } else {
                    $height = ipGetOption('Content.widgetImageHeight', 900);
                }
                $transform = array(
                    'type' => 'fit',
                    'width' => $width,
                    'height' => $height
                );
            }
            try {
                $data['imageSmall'] = ipFileUrl(ipReflection($data['imageOriginal'], $transform, $desiredName));
            } catch (\Ip\Exception\TransformException $e) {
                ipLog()->error($e->getMessage(), array('errorTrace' => $e->getTraceAsString()));
            }


            if (empty($data['type'])) {
                $data['type'] = 'lightbox';
            }
            if (empty($data['url'])) {
                $data['url'] = '';
            }
            if (empty($data['blank'])) {
                $data['blank'] = '';
            }
            if (empty($data['title'])) {
                $data['title'] = '';
            }
            if (empty($data['description'])) {
                $data['description'] = '';
            }


        }
        return parent::generateHtml($revisionId, $widgetId, $data, $skin);
    }

    public function adminHtmlSnippet()
    {
        $variables = array (
            'linkForm' => $this->linkForm(),
            'settingsForm' => $this->settingsForm()
        );
        return ipView('snippet/image.php', $variables)->render();

    }

    protected function linkForm()
    {
        $form = new \Ip\Form();
        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);


        $field = new \Ip\Form\Field\Select(
            array(
                'name' => 'type',
                'label' => __('Mouse click action', 'Ip-admin', false),
            ));

        $values = array(
            array('lightbox', __('Lightbox', 'Ip-admin', false)),
            array('link', __('URL', 'Ip-admin', false)),
            array('none', __('None', 'Ip-admin', false)),
        );
        $field->setValues($values);
        $form->addfield($field);


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'url',
                'label' => __('Url', 'Ip-admin', false),
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'blank',
                'label' => __('Open in new window', 'Ip-admin', false),
            ));
        $form->addField($field);

        return $form; // Output a string with generated HTML form
    }

    protected function settingsForm()
    {
        $form = new \Ip\Form();



        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'title',
                'label' => __('Title', 'Ip-admin', false),
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Textarea(
            array(
                'name' => 'description',
                'label' => __('Description', 'Ip-admin', false),
            ));
        $form->addField($field);



        return $form; // Output a string with generated HTML form
    }


}
