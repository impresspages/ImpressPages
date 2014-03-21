<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Log;


class AdminController extends \Ip\GridController
{
    protected function config()
    {
        return array(
            'type' => 'table',
            'table' => 'log',
            'allowCreate' => FALSE,
            'allowUpdate' => FALSE,
            'allowDelete' => FALSE,
            'sortField' => 'id',
            'sortDirection' => 'desc',
            'fields' => array(
                array(
                    'label' => __('Time', 'Ip-admin', FALSE),
                    'field' => 'time'
                ),
                array(
                    'label' => __('Message', 'Ip-admin', FALSE),
                    'field' => 'message',
                    'preview' => __CLASS__ . '::previewMessage'
                ),
                array(
                    'label' => __('Context', 'Ip-admin', FALSE),
                    'field' => 'context',
                    'preview' => __CLASS__ . '::previewContext'
                )
            )
        );
    }

    public static function previewMessage($value, $recordData)
    {
        $context = json_decode($recordData['context'], TRUE);

        $replace = array();
        foreach ($context as $key => $val) {
            if (is_string($val) || is_numeric($val)) {
                $replace['{' . $key . '}'] = '<em>' . esc($val) . '</em>';
            }
        }

        $message = esc($recordData['message']);

        return strtr($message, $replace);
    }

    public static function previewContext($value, $recordData)
    {
        $context = json_decode($recordData['context'], TRUE);

        unset($context['exception']);

        if (function_exists('ob_start')) {
            ob_start();
            var_dump($context);
            return ob_get_clean();
        } else {
            return var_export($context, TRUE);
        }
    }
}
