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
            'allowCreate' => false,
            'allowUpdate' => false,
            'allowDelete' => false,
            'sortField' => 'id',
            'sortDirection' => 'desc',
            'fields' => array(
                array(
                    'label' => __('Time', 'Ip-admin', false),
                    'field' => 'time'
                ),
                array(
                    'label' => __('Message', 'Ip-admin', false),
                    'field' => 'message',
                    'preview' => __CLASS__ . '::previewMessage'
                ),
                array(
                    'label' => __('Context', 'Ip-admin', false),
                    'field' => 'context',
                    'preview' => __CLASS__ . '::previewContext'
                )
            )
        );
    }

    public static function previewMessage($value, $recordData)
    {
        $context = json_decode($recordData['context'], true);

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
        $context = json_decode($recordData['context'], true);

        unset($context['exception']);

        if (function_exists('ob_start')) {
            ob_start();
            var_dump($context);
            return ob_get_clean();
        } else {
            return var_export($context, true);
        }
    }
}
