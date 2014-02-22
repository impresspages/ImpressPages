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
                    'label' => __('Recipient name', 'ipAdmin', FALSE),
                    'field' => 'toName',
                    'preview' => FALSE
                ),
                array(
                    'label' => __('Recipient email', 'ipAdmin', FALSE),
                    'field' => 'to',
                    'preview' => __CLASS__ . '::to'
                ),
                array(
                    'label' => __('Sender name', 'ipAdmin', FALSE),
                    'field' => 'fromName',
                    'preview' => FALSE
                ),
                array(
                    'label' => __('Sender email', 'ipAdmin', FALSE),
                    'field' => 'from',
                    'preview' => __CLASS__ . '::from'
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

    public static function to($value, $recordData)
    {
        return esc($recordData['toName'] . ' ' . $value);
    }

    public static function from($value, $recordData)
    {
        return esc($recordData['fromName'] . ' ' . $value);
    }




}
