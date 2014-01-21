<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Content\Widget\Form;




class Controller extends \Ip\WidgetController{


    public function getTitle() {
        return __('Form', 'ipAdmin', false);
    }

    public function post ($instanceId, $data) {
        $postData = ipRequest()->getPost();

        $form = $this->createForm($instanceId, $data);
        $errors = $form->validate($postData);

        if ($errors) {
            $data = array(
                'status' => 'error',
                'errors' => $errors
            );
        } else {
            $this->sendEmail($form, $postData, $data);

            $data = array(
                'status' => 'success'
            );
        }

        // TODO use JsonRpc
        return new \Ip\Response\Json($data);
    }

    public function adminSnippets()
    {
        $snippets = array();

        $fieldObjects = Model::getAvailableFieldTypes();

        $fieldTypes = array ();
        foreach($fieldObjects as $fieldObject){
            $fieldTypes[] = array(
                'key' => $fieldObject->getKey(),
                'title' => $fieldObject->getTitle()
            );
        }
        usort($fieldTypes, array($this, 'sortFieldTypes'));
        $data['fieldTypes'] = $fieldTypes;

        $snippets[] = ipView('snippet/popup.php', $data)->render();
        return $snippets;

    }

    public function sendEmail ($form, $postData, $data) {

        $contentData = array();

        $websiteName = ipGetOption('Config.websiteTitle');
        $websiteEmail = ipGetOption('Config.websiteEmail');


        $to = $from = $websiteEmail;
        $files = array();

        foreach($form->getFields() as $fieldKey => $field) {

            if ($field->getType() == \Ip\Form\Field::TYPE_REGULAR) {
                if (!isset($postData[$field->getName()])) {
                    $postData[$field->getName()] = null;
                }

                $title = $field->getLabel();
                $value = $field->getValueAsString($postData, $field->getName());
                $contentData[] = array(
                    'fieldClass' => get_class($field),
                    'title' => $title,
                    'value' => $value
                );
            }

            if (get_class($field) == 'Ip\Form\Field\Email') {
                $userFrom = $field->getValueAsString($postData, $field->getName());
                if ($userFrom != '') {
                    $from = $userFrom;
                }
            }


            if (get_class($field) == 'Ip\Form\Field\File') {
                /**
                 * @var $uploadedFiles \Ip\Form\Field\Helper\UploadedFile[]
                 */
                $uploadedFiles = $field->getFiles($postData, $field->getName());
                foreach($uploadedFiles as $uploadedFile) {
                    $files[] = array($uploadedFile->getFile(),$uploadedFile->getOriginalFileName());
                }
            }
        }
        $content = ipView('helperView/email_content.php', array('values' => $contentData))->render();


        $emailData = array(
            'content' => $content
        );


        $email = ipEmailTemplate($emailData);


        //get page where this widget sits :)
        $fullWidgetRecord = \Ip\Internal\Content\Model::getWidgetFullRecord($postData['instanceId']);
        $pageTitle = '';
        if (isset($fullWidgetRecord['revisionId'])) {
            $revision = \Ip\Internal\Revision::getRevision($fullWidgetRecord['revisionId']);
            if (isset($revision['zoneName']) && $revision['pageId']) {
                $pageTitle = ipContent()->getZone($revision['zoneName'])->getPage($revision['pageId'])->getNavigationTitle();
            }
        }

        $subject = $websiteName.': '.$pageTitle;

        $emailQueue = new \Ip\Internal\Email\Module();
        $emailQueue->addEmail($from, '', $to, '',  $subject, $email, false, true, $files);

        $emailQueue->send();

    }



    public function defaultData()
    {
        $data = array();
        $data['fields'] = array();
        $data['fields'][] = array (
            'type' => 'Text',
            'label' => __('Name', 'ipPublic', false),
            'options' => array()
        );
        $data['fields'][] = array (
            'type' => 'IpEmail',
            'label' => __('Email', 'ipPublic', false),
            'options' => array()
        );
        $data['fields'][] = array (
            'type' => 'Textarea',
            'label' => __('Text', 'ipPublic', false),
            'options' => array()
        );
        return $data;
    }

    public function generateHtml($revisionId, $widgetId, $instanceId, $data, $layout) {

        $data['form'] = $this->createForm($instanceId, $data);

        if (!isset($data['success'])) {
            $data['success'] = '';
        }



        return parent::generateHtml($revisionId, $widgetId, $instanceId, $data, $layout);
    }


    public function dataForJs($revisionId, $widgetId, $instanceId, $data, $layout) {
        //collect available field types
        $fieldTypeObjects = Model::getAvailableFieldTypes();

        $fieldTypes = array ();
        foreach($fieldTypeObjects as $typeObject){
            $fieldTypes[$typeObject->getKey()] = array(
                'key' => $typeObject->getKey(),
                'title' => $typeObject->getTitle(),
                'optionsInitFunction' => $typeObject->getJsOptionsInitFunction(),
                'optionsSaveFunction' => $typeObject->getJsOptionsSaveFunction(),
                'optionsHtml' => $typeObject->getJsOptionsHtml()
            );
        }
        $data['fieldTypes'] = $fieldTypes;

        if (empty($data['fields'])) {
            $data['fields'] = array();
            $data['fields'][] = array (
                'type' => 'Text',
                'label' => __('Name', 'ipPublic', false),
                'options' => array()
            );
            $data['fields'][] = array (
                'type' => 'IpEmail',
                'label' => __('Email', 'ipPublic', false),
                'options' => array()
            );
            $data['fields'][] = array (
                'type' => 'Textarea',
                'label' => __('Text', 'ipPublic', false),
                'options' => array()
            );
        }



        return $data;
    }

    /**
     *
     *
     * @param int $instanceId
     * @param array $data
     * @return \Ip\Form
     */
    protected function createForm($instanceId, $data) {
        $form = new \Ip\Form();

        if (empty($data['fields']) || !is_array($data['fields'])) {
            $data['fields'] = array();
        }
        foreach ($data['fields'] as $fieldKey => $field) {
            if (!isset($field['type']) || !isset($field['label'])) {
                continue;
            }
            if (!isset($field['options'])) {
                $field['options'] = array();
            }
            if (!isset($field['options']) || !is_array($field['options'])) {
                $field['options'] = array();
            }
            if (!isset($field['required'])) {
                $field['required'] = false;
            }
            $fieldType = Model::getFieldType($field['type']);
            if ($fieldType) {
                $fieldData = array (
                    'label' => $field['label'],
                    'name' => 'ipForm_field_'.$fieldKey,
                    'required' => $field['required'],
                    'options' => $field['options']
                );

                try {
                    $newField = $fieldType->createField($fieldData);
                    $form->addField($newField);
                } catch (\Ip\Internal\Content\Exception $e) {
                    ipLog()->error('FormWidget.failedAddField: Widget failed to add field.', array('widget' => 'Form', 'exception' => $e, 'fieldData' => $fieldData));
                }

            }
        }



        //special variable to post to widget controller
        $field = new \Ip\Form\Field\Hidden(
        array(
        'name' => 'sa',
        'value' => 'Content.widgetPost'
        ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Hidden(
        array(
        'name' => 'instanceId',
        'value' => $instanceId
        ));
        $form->addField($field);

        //antispam
        $field = new \Ip\Form\Field\Antispam(
        array(
        'name' => 'checkField'
        ));
        $form->addField($field);

        //submit
        $field = new \Ip\Form\Field\Submit(
        array(
            'value' => __('Send', 'ipPublic', false)
        ));
        $form->addField($field);



        return $form;
    }

    protected function sortFieldTypes($a, $b) {
        return strcasecmp($a['title'], $b['title']);
    }
}
