<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
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
        foreach($form->getFields() as $fieldKey => $field) {
            
            if (get_class($field) != 'Library\IpForm\Field\Hidden' && get_class($field) != 'Library\IpForm\Field\Submit') {
                if (!isset($postData[$field->getName()])) {
                    $postData[$field->getName()] = null;
                }
                
                $title = $field->getLabel();
                $value = $field->getValueAsString($postData[$field->getName()]);
                $contentData[] = array(
                    'title' => $title,
                    'value' => $value 
                );
            }
        }
        $content = \Ip\View::create('view/email_content.php', array('values' => $contentData))->render();
        $websiteName = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name');
        $websiteEmail = $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email'); 
        
        
        $emailData = array(
            'content' => $content,
            'name' => $websiteName,
            'email' => $websiteEmail
        );
        
        $email = \Ip\View::create('view/email.php', $emailData)->render();
        $to = $from = $websiteEmail;
        
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
        
        $files = array(); //TODO file handling in IpForm widget is not implemented yet.
        $emailQueue = new \Modules\administrator\email_queue\Module();
        $emailQueue->addEmail($from, '', $to, '',  $subject, $email, false, true, $files);

        $emailQueue->send();
        
    }
    
    
    public function managementHtml($instanceId, $data, $layout) {
        $addFieldForm = new \Library\IpForm\Form();
        $addFieldForm->addAttribute('class', 'ipaButton ipaFormAddField');
        
        //collect available field types
        $fieldObjects = IpForm\Model::getAvailableFieldTypes();
        
        $fieldTypes = array ();
        foreach($fieldObjects as $fieldObject){
            $fieldTypes[] = array(
                'key' => $fieldObject->getKey(),
                'title' => $fieldObject->getTitle()
            );
        }
        $data['fieldTypes'] = $fieldTypes;
        
        //create add field button
        $field = new \Library\IpForm\Field\Submit(
        array(
        'defaultValue' => 'Add'
        )
        );
        $addFieldForm->addField($field);
        
        
        $data['addFieldForm'] = $addFieldForm;
        return parent::managementHtml($instanceId, $data, $layout);
    }
    
    public function previewHtml($instanceId, $data, $layout) {

        $data['form'] = $this->createForm($instanceId, $data);
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
     * @param unknown_type $instanceId
     * @param unknown_type $data
     * @return \Library\IpForm\Form
     */
    private function createForm($instanceId, $data) {
        $form = new \Library\IpForm\Form();
        
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
                
                $newField = $fieldType->createField($fieldData);
                $form->addField($newField);
            }
        }
        
        

        //special variables to post to widget controller
        $field = new \Library\IpForm\Field\Hidden(
        array(
        'name' => 'g',
        'defaultValue' => 'standard'
        ));
        $form->addField($field);

        $field = new \Library\IpForm\Field\Hidden(
        array(
        'name' => 'm',
        'defaultValue' => 'content_management'
        ));
        $form->addField($field);
        
        $field = new \Library\IpForm\Field\Hidden(
        array(
        'name' => 'a',
        'defaultValue' => 'widgetPost'
        ));
        $form->addField($field);
        
        $field = new \Library\IpForm\Field\Hidden(
        array(
        'name' => 'instanceId',
        'defaultValue' => $instanceId
        ));
        $form->addField($field);
        
        $field = new \Library\IpForm\Field\Submit(
        array(
        'defaultValue' => 'Submit'
        ));

        $form->addField($field);
        return $form;
    }
}