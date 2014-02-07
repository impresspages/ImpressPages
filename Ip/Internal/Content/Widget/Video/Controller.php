<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Content\Widget\Video;




class Controller extends \Ip\WidgetController
{
    public function getTitle() {
        return __('Video', 'ipAdmin', false);
    }


    public function generateHtml($revisionId, $widgetId, $instanceId, $data, $skin)
    {
        if (!empty($data['url'])) {
            $videoHtml = $this->generateVideoHtml($data['url']);
            if ($videoHtml) {
                $data['videoHtml'] = $videoHtml;
            }
        }

        return parent::generateHtml($revisionId, $widgetId, $instanceId, $data, $skin);
    }


    protected function generateVideoHtml($url)
    {
        if (empty($url)) {
            return false;
        }


        if (preg_match('/^((http|https):\/\/)?(www.)?youtube.com/s', $url)) {
            if (!preg_match('/^((http|https):\/\/)/s', $url)) {
                $url = 'http://' . $url;
            }
            if (preg_match('/youtube.com\/watch\?v=/s', $url)) {
                $url = str_replace('youtube.com/watch?v=', 'youtube.com/embed/', $url);
            }
            if (ipIsManagementState()) {
                if (preg_match('/\?/s', $url)) {
                    $url .= '&wmode=opaque';
                } else {
                    $url .= '?wmode=opaque';
                }

            }

            $variables = array(
                'url' => $url
            );

            return ipView('view/youtube.php', $variables)->render();

        }

    }

    public function adminHtmlSnippet()
    {


        $form = $this->editForm();
        $variables = array(
            'form' => $form
        );

        return ipView('snippet/edit.php', $variables)->render();
    }

    protected function editForm()
    {
        $form = new \Ip\Form();

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'url',
                'label' => __('Url', 'ipAdmin', false),
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'url',
                'label' => __('Url', 'ipAdmin', false),
            ));
        $form->addField($field);


        return $form; // Output a string with generated HTML form
    }

}
