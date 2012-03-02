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
    
    public function managementHtml($instanceId, $data, $layout) {
        $addFieldForm = new \Library\IpForm\Form();
        $addFieldForm->addAttribute('class', 'ipaButton ipaFormAddField');
        
        //collect available field types
        $fieldObjects = IpForm\Model::getAvailableFieldTypes();
        
        $fieldTypes = array ();
        foreach($fieldObjects as $fieldObject){
            $fieldTypes[] = array('key' => $fieldObject->getKey(), 'title' => $fieldObject->getTitle());
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
        $form = new \Library\IpForm\Form();
        
        if (!$data['fields'] || !is_array($data['fields'])) {
            $data['fields'] = array();
        }
        foreach ($data['fields'] as $fieldKey => $field) {
            if (!isset($field['type'])) {
                continue;
            }
            if (!isset($field['options']) || !is_array($field['options'])) {
                $field['options'] = array();
            }
            $fieldType = IpForm\Model::getFieldType($field['type']);
            if ($fieldType) {
                $field['options']['label'] = $field['label'];
                $field['options']['name'] = 'input_'.$fieldKey;
                
                $newField = $fieldType->createField($field['options']);
                $form->addField($newField);
            }
        }
        

        $field = new \Library\IpForm\Field\Submit(
        array(
        'defaultValue' => 'Submit'
        )
        );

        $form->addField($field);
                
        
        $data['form'] = $form;
        
        return parent::previewHtml($instanceId, $data, $layout);
    }    
}