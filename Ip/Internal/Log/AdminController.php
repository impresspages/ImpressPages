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
            'allowInsert' => false,
            'allowUpdate' => false,
            'allowDelete' => false,
            'fields' => array(
                array(
                    'label' => __('Time', 'ipAdmin', false),
                    'field' => 'time'
                ),
                array(
                    'label' => __('Message', 'ipAdmin', false),
                    'field' => 'message',
                    'filter' => __CLASS__ . '::filterMessage'
                ),
                array(
                    'label' => __('Context', 'ipAdmin', false),
                    'field' => 'context',
                    'filter' => __CLASS__ . '::filterContext'
                )
            )
        );
    }

    public static function filterMessage($value, $recordData)
    {
        $context = json_decode($recordData['context'], true);

        $replace = array();
        foreach ($context as $key => $val) {
            if (is_string($val) || is_numeric($val)) {
                $replace['{' . $key . '}'] = '<em>' . $val . '</em>';
            }
        }

        return strtr($recordData['message'], $replace);
    }

    public static function filterContext($value, $recordData)
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