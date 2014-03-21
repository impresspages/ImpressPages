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
                    'label' => __('Subject', 'Ip-admin', FALSE),
                    'field' => 'subject'
                ),
                array(
                    'label' => __('Email', 'Ip-admin', FALSE),
                    'field' => 'email',
                    'preview' => __CLASS__ . '::html2text'
                ),
                array(
                    'label' => __('Recipient name', 'Ip-admin', FALSE),
                    'field' => 'toName',
                    'preview' => FALSE
                ),
                array(
                    'label' => __('Recipient email', 'Ip-admin', FALSE),
                    'field' => 'to',
                    'preview' => __CLASS__ . '::to'
                ),
                array(
                    'label' => __('Sender name', 'Ip-admin', FALSE),
                    'field' => 'fromName',
                    'preview' => FALSE
                ),
                array(
                    'label' => __('Sender email', 'Ip-admin', FALSE),
                    'field' => 'from',
                    'preview' => __CLASS__ . '::from'
                ),
                array(
                    'label' => __('Sent at', 'Ip-admin', FALSE),
                    'field' => 'send'
                ),
                array(
                    'label' => __('Attachment', 'Ip-admin', FALSE),
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

    public static function to($value, $recordData)
    {
        return esc($recordData['toName'] . ' ' . $value);
    }

    public static function from($value, $recordData)
    {
        return esc($recordData['fromName'] . ' ' . $value);
    }




}
