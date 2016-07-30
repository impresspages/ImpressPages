<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Config;


class Forms
{
    public static function getForm()
    {
        $form = new \Ip\Form();
        $form->addClass('ipsConfigForm');
        $form->setAjaxSubmit(0);


        $field = new FieldOptionTextLang(
            array(
                'optionName' => 'Config.websiteTitle',
                'name' => 'websiteTitle', //html "name" attribute
                'label' => __('Website title', 'Ip-admin', false), //field label that will be displayed next to input field
                'hint' => __('Used as a sender name in emails and as default website logo.', 'Ip-admin')
            ));
        $field->addClass('ipsAutoSave');
        $form->addField($field);


        $field = new FieldOptionTextLang(
            array(
                'optionName' => 'Config.websiteEmail',
                'name' => 'websiteEmail', //html "name" attribute
                'value' => ipGetOptionLang('Config.websiteEmail'),
                'label' => __('Website email', 'Ip-admin', false), //field label that will be displayed next to input field
                'hint' => __('Email address used as a sender to send emails on behalf of the website.', 'Ip-admin')
            ));
        $field->addValidator('Email');
        $field->addClass('ipsAutoSave');
        $form->addField($field);


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'gmapsApiKey',
                'value' => ipGetOption('Config.gmapsApiKey'),
                'label' => __('Google Maps API key', 'Ip-admin', false),
                'note' => __('You must provide Google Maps API key for Map widget to work.', 'Ip-admin', false) . ' <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">' . __('Follow instructions.', 'Ip-admin', false) . '</a>'
            ));
        $field->addClass('ipsAutoSave');
        $field->addAttribute('data-fieldid', $field->getName());
        $field->addAttribute('id', $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $form->addField($field);

        return $form;
    }


    public static function getAdvancedForm()
    {
        $form = new \Ip\Form();
        $form->addClass('ipsConfigForm');
        $form->addClass('ipsConfigFormAdvanced');
        $form->addClass('hidden');
        $form->setAjaxSubmit(0);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'automaticCron',
                //html "name" attribute
                'value' => ipGetOption('Config.automaticCron', 1),
                'label' => __('Execute cron automatically', 'Ip-admin', false),
                //field label that will be displayed next to input field
                'hint' => __(
                    'ImpressPages execute cron once every hour on randomly selected visitor page load. I you have setup cron manually, you can disable automatic cron functionality.',
                    'Ip-admin'
                ),
            ));
        $field->addClass('ipsAutoSave');
        $field->addAttribute('data-fieldid', $field->getName());
        $field->addAttribute('id', $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'cronPassword', //html "name" attribute
                'value' => ipGetOption('Config.cronPassword', 1),
                'label' => __('Cron password', 'Ip-admin', false), //field label that will be displayed next to input field
                'hint' => __('Protect cron from being abusively executed by the strangers.', 'Ip-admin', false),
                'note' => '<span class="ipsUrlLabel">' . __(
                        'Cron URL: ',
                        'Ip-admin'
                    ) . '</span><a target="_blank" class="ipsUrl"></a>'
            ));
        $field->addClass('ipsAutoSave');
        $field->addAttribute('data-fieldid', $field->getName());
        $field->addAttribute('id', $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $form->addField($field);




        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'removeOldEmails',
                //html "name" attribute
                'value' => ipGetOption('Config.removeOldEmails', 0),
                'label' => __('Remove old emails from the log', 'Ip-admin', false)
            ));
        $field->addClass('ipsAutoSave');
        $field->addAttribute('data-fieldid', $field->getName());
        $field->addAttribute('id', $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $form->addField($field);




        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'removeOldEmailsDays',
                //html "name" attribute
                'value' => ipGetOption('Config.removeOldEmailsDays', 720),
                'label' => __('Days to keep emails', 'Ip-admin', false),
                'hint' => __('Meaningful only if "Remove old emails" is on.', 'Ip-admin', false)
            ));
        $field->addClass('ipsAutoSave');
        $field->addAttribute('data-fieldid', $field->getName());
        $field->addAttribute('id', $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $form->addField($field);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'removeOldRevisions',
                //html "name" attribute
                'value' => ipGetOption('Config.removeOldRevisions', 0),
                'label' => __('Remove old page revisions', 'Ip-admin', false)
            ));
        $field->addClass('ipsAutoSave');
        $field->addAttribute('data-fieldid', $field->getName());
        $field->addAttribute('id', $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $form->addField($field);




        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'removeOldRevisionsDays',
                //html "name" attribute
                'value' => ipGetOption('Config.removeOldRevisionsDays', 720),
                'label' => __('Days to keep revisions', 'Ip-admin', false),
                'hint' => __('Meaningful only if "Remove old page revisions" is on.', 'Ip-admin', false)
            ));
        $field->addClass('ipsAutoSave');
        $field->addAttribute('data-fieldid', $field->getName());
        $field->addAttribute('id', $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $form->addField($field);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'allowAnonymousUploads',
                //html "name" attribute
                'value' => ipGetOption('Config.allowAnonymousUploads', 1),
                'label' => __('Allow anonymous uploads', 'Ip-admin', false),
                'hint' => __('Disabling this feature will prevent users from uploading files to your website. E.g. in contact forms.', 'Ip-admin')
            ));
        $field->addClass('ipsAutoSave');
        $field->addAttribute('data-fieldid', $field->getName());
        $field->addAttribute('id', $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $form->addField($field);



        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'trailingSlash',
                //html "name" attribute
                'value' => ipGetOption('Config.trailingSlash', 1),
                'label' => __('Add trailing slash at the end of page URL', 'Ip-admin', false),
                'hint' => __('This won\'t change existing URLs. Only new and updated pages will get slash at the end.', 'Ip-admin')
            ));
        $field->addClass('ipsAutoSave');
        $field->addAttribute('data-fieldid', $field->getName());
        $field->addAttribute('id', $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $form->addField($field);


        return $form;
    }
}
