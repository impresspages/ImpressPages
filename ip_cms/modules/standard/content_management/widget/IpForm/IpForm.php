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
        return $parametersMod->getValue('standard', 'content_management', 'widget_text', 'menu_mod_text');
    }
    
    public function managementHtml($instanceId, $data, $layout) {
        global $dispatcher;
        $addFieldForm = new \Library\IpForm\Form();
        $addFieldForm->addAttribute('class', 'ipaButton ipaFormAddField');
        
        //collect available field types
        $event = new \Modules\standard\content_management\EventFormFields(null, 'contentManagement.collectFieldTypes', null);
        $dispatcher->notify($event);
        $fieldObjects = $event->getFields();
        
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
        
        $field = new \Library\IpForm\Field\Text(
        array(
        'label' => 'Label',  //Field label
        'name' => 'name',  //Input (post variable) name
        'required' => 'false'  //Database field name
        )
        );
        $form->addField($field);

        $field = new \Library\IpForm\Field\Email(
        array(
        'label' => 'Label2',  //Field label
        'name' => 'name2',  //Input (post variable) name
        'note' => 'Simple note',
        'hint' => 'Hint'
        )
        );
        $field->addValidator('Required');
        
        $form->addField($field);


        $field = new \Library\IpForm\Field\Submit(
        array(
        'label' => 'Label2',  //Field label
        'name' => 'name2',  //Input (post variable) name
        'note' => 'Simple note',
        'hint' => 'Hint'
        )
        );

        $form->addField($field);
                
        
        $data['form'] = $form;
        
        return parent::previewHtml($instanceId, $data, $layout);
    }    
}