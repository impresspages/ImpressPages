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


        $field = new FieldOptionTextLang(
            array(
                'optionName' => 'Config.websiteDescription',
                'name' => 'websiteDescription',
                'label' => __('Website description', 'Ip-admin', false),
                'hint' => __('One or two sentences about the website. Used as the summary in llms.txt.', 'Ip-admin')
            ));
        $field->addClass('ipsAutoSave');
        $form->addField($field);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'sitemapEnabled',
                'value' => ipGetOption('Config.sitemapEnabled', 1),
                'label' => __('Publish sitemap.xml', 'Ip-admin', false),
                'hint' => __('Lists every public page for search engines at /sitemap.xml and links it from /robots.txt.', 'Ip-admin')
            ));
        $field->addClass('ipsAutoSave');
        $field->addAttribute('data-fieldid', $field->getName());
        $field->addAttribute('id', $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $form->addField($field);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'llmsTxtEnabled',
                'value' => ipGetOption('Config.llmsTxtEnabled', 1),
                'label' => __('Publish llms.txt', 'Ip-admin', false),
                'hint' => __('A plain-text summary of the website and its pages for AI assistants, at /llms.txt.', 'Ip-admin')
            ));
        $field->addClass('ipsAutoSave');
        $field->addAttribute('data-fieldid', $field->getName());
        $field->addAttribute('id', $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        $form->addField($field);


        $form->addField(self::autoSave(new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'llmsFullTxtEnabled',
                'value' => ipGetOption('Config.llmsFullTxtEnabled', 0),
                'label' => __('Publish llms-full.txt', 'Ip-admin', false),
                'hint' => __('The text of every public page in one file for AI assistants, at /llms-full.txt.', 'Ip-admin')
            ))));


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


        $form->addFieldset(new \Ip\Form\Fieldset(__('Structured data', 'Ip-admin', false)));

        $form->addField(self::autoSave(new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'structuredDataEnabled',
                'value' => ipGetOption('Config.structuredDataEnabled', 1),
                'label' => __('Add structured data to pages', 'Ip-admin', false),
                'hint' => __('Describes the website, its owner and each page\'s place in the menu to search engines and AI assistants (schema.org JSON-LD).', 'Ip-admin')
            ))));

        $form->addField(self::autoSave(new \Ip\Form\Field\Select(
            array(
                'name' => 'organizationType',
                'value' => ipGetOption('Config.organizationType', 'Organization'),
                'label' => __('Website owner', 'Ip-admin', false),
                'values' => array(
                    array('Organization', __('Organisation', 'Ip-admin', false)),
                    array('LocalBusiness', __('Local business with an address customers visit', 'Ip-admin', false)),
                ),
            ))));

        $form->addField(self::autoSave(new \Ip\Form\Field\Text(
            array(
                'name' => 'organizationTelephone',
                'value' => ipGetOption('Config.organizationTelephone', ''),
                'label' => __('Telephone', 'Ip-admin', false),
            ))));

        $addressLabels = array(
            'organizationStreetAddress' => __('Street address', 'Ip-admin', false),
            'organizationLocality' => __('City or town', 'Ip-admin', false),
            'organizationRegion' => __('State or region', 'Ip-admin', false),
            'organizationPostalCode' => __('Postcode', 'Ip-admin', false),
            'organizationCountry' => __('Country', 'Ip-admin', false),
        );
        foreach (\Ip\Internal\Seo\Model::addressFields() as $option => $property) {
            $form->addField(self::autoSave(new \Ip\Form\Field\Text(
                array(
                    'name' => $option,
                    'value' => ipGetOption('Config.' . $option, ''),
                    'label' => $addressLabels[$option],
                ))));
        }

        $form->addField(self::autoSave(new \Ip\Form\Field\Textarea(
            array(
                'name' => 'organizationSameAs',
                'value' => ipGetOption('Config.organizationSameAs', ''),
                'label' => __('Profiles elsewhere', 'Ip-admin', false),
                'hint' => __('Links to the owner\'s social media, map listing or directory pages, one per line. Helps search engines and AI assistants recognise the owner.', 'Ip-admin')
            ))));


        $form->addFieldset(new \Ip\Form\Fieldset(__('AI crawlers', 'Ip-admin', false)));

        foreach (\Ip\Internal\Seo\Model::aiCrawlers() as $userAgent => $operator) {
            $option = \Ip\Internal\Seo\Model::aiCrawlerOption($userAgent);
            $form->addField(self::autoSave(new \Ip\Form\Field\Checkbox(
                array(
                    'name' => $option,
                    'value' => ipGetOption('Config.' . $option, 1),
                    'label' => sprintf(__('Allow %s (%s)', 'Ip-admin', false), $userAgent, $operator),
                ))));
        }
        $form->getField(\Ip\Internal\Seo\Model::aiCrawlerOption('GPTBot'))->setHint(
            __('Unticked crawlers are asked in /robots.txt not to read the website. AI assistants can only quote and link pages their crawler may read.', 'Ip-admin')
        );

        return $form;
    }

    /**
     * Marks a field for saving as soon as it changes.
     *
     * @param \Ip\Form\Field $field
     * @return \Ip\Form\Field
     */
    private static function autoSave($field)
    {
        $field->addClass('ipsAutoSave');
        $field->addAttribute('data-fieldid', $field->getName());
        $field->addAttribute('id', $field->getName());
        $field->addAttribute('data-fieldname', $field->getName());
        return $field;
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
