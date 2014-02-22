<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Email;



class AdminController extends \Ip\GridController
{
    protected function config()
    {
        return array (
            'type' => 'table',
            'allowCreate' => false,
            'allowUpdate' => false,
            'allowDelete' => false,
            'table' => 'emailQueue',
            'actions' => array(),
            'fields' => array (
                array(
                    'label' => __('Subject', 'ipAdmin', FALSE),
                    'field' => 'subject'
                ),
                array(
                    'label' => __('Email', 'ipAdmin', FALSE),
                    'field' => 'email',
                    'preview' => __CLASS__ . '::html2text'
                ),
                array(
                    'label' => __('To', 'ipAdmin', FALSE),
                    'field' => 'toName'
                ),
                array(
                    'label' => __('To', 'ipAdmin', FALSE),
                    'field' => 'to'
                ),
                array(
                    'label' => __('From', 'ipAdmin', FALSE),
                    'field' => 'fromName'
                ),
                array(
                    'label' => __('From', 'ipAdmin', FALSE),
                    'field' => 'from'
                ),
                array(
                    'label' => __('Sent at', 'ipAdmin', FALSE),
                    'field' => 'send'
                ),
                array(
                    'label' => __('Attachment', 'ipAdmin', FALSE),
                    'field' => 'fileNames'
                )

            )
        );
    }

    public static function html2text($value, $recordData)
    {
        $html2text = new \Ip\Internal\Text\Html2Text('<html><body>'.$value.'</body></html>', false);
        $text = $html2text->get_text();
        return $text;
    }

}
