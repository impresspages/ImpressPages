<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\content_management\widget;

if (!defined('CMS')) exit;



class IpForm extends \Modules\standard\content_management\Widget{


    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('standard', 'content_management', 'widget_contact_form', 'contact_form');
    }
    
    public function post ($controller, $instanceId, $postData, $data) {
        
        
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
        
        $controller->returnJson($data);
    }
    
    public function sendEmail ($form, $postData, $data) {
        global $parametersMod;
        global $site;
        
        $contentData = array();

        $websiteName = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name');
        $websiteEmail = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email');


        $to = $from = $websiteEmail;
        $files = array();

        foreach($form->getFields() as $fieldKey => $field) {
            
            if ($field->getType() == \Modules\developer\form\Field\Field::TYPE_REGULAR) {
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

            if (get_class($field) == 'Modules\developer\form\Field\Email') {
                $userFrom = $field->getValueAsString($postData, $field->getName());
                if ($userFrom != '') {
                    $from = $userFrom;
                }
            }


            if (get_class($field) == 'Modules\developer\form\Field\File') {
                /**
                 * @var $uploadedFiles \Modules\developer\form\Field\Helper\UploadedFile[]
                 */
                $uploadedFiles = $field->getFiles($postData, $field->getName());
                foreach($uploadedFiles as $uploadedFile) {
                    $files[] = array(
                        'real_name' => $uploadedFile->getFile(),
                        'required_name' => $uploadedFile->getOriginalFileName()
                    );
                }
            }
        }
        $content = \Ip\View::create('view/email_content.php', array('values' => $contentData))->render();

        
        $emailData = array(
            'content' => $content,
            'name' => $websiteName,
            'email' => $websiteEmail
        );
        
        $email = \Ip\View::create('view/email.php', $emailData)->render();

        
        //get page where this widget sits :)
        $fullWidgetRecord = \Modules\standard\content_management\Model::getWidgetFullRecord($postData['instanceId']);
        $pageTitle = '';
        if (isset($fullWidgetRecord['revisionId'])) {
            $revision = \Ip\Revision::getRevision($fullWidgetRecord['revisionId']);
            if (isset($revision['zoneName']) && $revision['pageId']) {
                $pageTitle = $site->getZone($revision['zoneName'])->getElement($revision['pageId'])->getButtonTitle();
            }
        }
        
        $subject = $websiteName.': '.$pageTitle;

        $emailQueue = new \Modules\administrator\email_queue\Module();
        $emailQueue->addEmail($from, '', $to, '',  $subject, $email, false, true, $files);

        $emailQueue->send();
        
    }
    
    
    public function managementHtml($instanceId, $data, $layout) {
        $fieldObjects = IpForm\Model::getAvailableFieldTypes();
        
        $fieldTypes = array ();
        foreach($fieldObjects as $fieldObject){
            $fieldTypes[] = array(
                'key' => $fieldObject->getKey(),
                'title' => $fieldObject->getTitle()
            );
        }
        usort($fieldTypes, array($this, 'sortFieldTypes'));
        $data['fieldTypes'] = $fieldTypes;

        
        return parent::managementHtml($instanceId, $data, $layout);
    }
    
    public function previewHtml($instanceId, $data, $layout) {

        $data['form'] = $this->createForm($instanceId, $data);
        
        if (!isset($data['success'])) {
            $data['success'] = '';
        }
        
        return parent::previewHtml($instanceId, $data, $layout);
    }
    
    
    public function dataForJs($data) {
        //collect available field types
        $fieldTypeObjects = IpForm\Model::getAvailableFieldTypes();
        
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
                'type' => 'IpText',
                'label' => '',
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
     * @return \Modules\developer\form\Form
     */
    private function createForm($instanceId, $data) {
        global $parametersMod;
        $form = new \Modules\developer\form\Form();
        
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
            $fieldType = IpForm\Model::getFieldType($field['type']);
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
                } catch (\Modules\standard\content_management\Exception $e) {
                    global $log;
                    $log->log('standard/content_management', 'create field', $e->getMessage());
                }
                
            }
        }
        
        

        //special variables to post to widget controller
        $field = new \Modules\developer\form\Field\Hidden(
        array(
        'name' => 'g',
        'defaultValue' => 'standard'
        ));
        $form->addField($field);

        $field = new \Modules\developer\form\Field\Hidden(
        array(
        'name' => 'm',
        'defaultValue' => 'content_management'
        ));
        $form->addField($field);
        
        $field = new \Modules\developer\form\Field\Hidden(
        array(
        'name' => 'a',
        'defaultValue' => 'widgetPost'
        ));
        $form->addField($field);
        
        $field = new \Modules\developer\form\Field\Hidden(
        array(
        'name' => 'instanceId',
        'defaultValue' => $instanceId
        ));
        $form->addField($field);

        //antispam
        $field = new \Modules\developer\form\Field\Check(
        array(
        'name' => 'checkFieldield'
        ));
        $form->addField($field);
        
        //submit
        $field = new \Modules\developer\form\Field\Submit(
        array(
        	'defaultValue' => $parametersMod->getValue('standard', 'content_management', 'widget_contact_form', 'send')
        ));
        $form->addField($field);
        
    

        return $form;
    }
    
    private function sortFieldTypes($a, $b) {
        return strcasecmp($a['title'], $b['title']);
    }
}