<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\community\newsletter\widget;

if (!defined('CMS')) exit;



class IpNewsletter extends \Modules\standard\content_management\Widget{


    public function getTitle() {
        global $parametersMod;
        return $parametersMod->getValue('community', 'newsletter', 'admin_translations', 'newsletter');
    }
    
    public function previewHtml($instanceId, $data, $layout) {
        global $session;
        global $site;
        global $parametersMod;
        $newsletterZone = $site->getZoneByModule('community', 'newsletter');
        if (!$newsletterZone) {
            return '
            Please create new zone in Developer / zones with associated module group <b>community</b> and module <b>newsletter</b>.
            ';
        }
        
        $data = array ();
        $data['form'] = $this->getForm();
        
        return parent::previewHtml($instanceId, $data, $layout);
    }

    /**
    * Return true if you like to hide widget in administration panel.
    * You will be able to access widget in your code.
    */
    public function getUnderTheHood() {
        global $site;
        $userZone = $site->getZoneByModule('community', 'newsletter');
        if ($userZone) {
            return false;
        } else {
            return true;
        }
    }    
    
    private function getForm() {
        global $parametersMod;
    
        $form = new \Modules\developer\form\Form();
    
        $field = new \Modules\developer\form\Field\Email(
        array(
            'name' => 'email',
            'label' => $parametersMod->getValue('community','newsletter','subscription_translations','label')
        ));
        $field->addValidator('Required');
        
        $form->addField($field);
    
    
        //Submit button
        $field = new \Modules\developer\form\Field\Submit(
        array(
            'name' => 'submit',
            'defaultValue' => $parametersMod->getValue('community','newsletter','subscription_translations','subscribe')
        ));
        $field->addAttribute('onclick', '$(this).closest(\'form\').data(\'tmp\', {buttonClicked: \'subscribe\'});');
        $form->addField($field);
    
        if ($parametersMod->getValue('community','newsletter','options','show_unsubscribe_button')) {
            $field = new \Modules\developer\form\Field\Submit(
            array(
                'name' => 'submit',
                'defaultValue' => $parametersMod->getValue('community','newsletter','subscription_translations','unsubscribe')
            ));
            $field->addAttribute('onclick', '$(this).closest(\'form\').data(\'tmp\', {buttonClicked: \'unsubscribe\'});');
            $form->addField($field);
        }
    
    
        return $form;
        
    }
}