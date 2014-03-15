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
                    'label' => __('Time', 'ipAdmin', FALSE),
                    'field' => 'time'
                ),
                array(
                    'label' => __('Message', 'ipAdmin', FALSE),
                    'field' => 'message',
                    'preview' => __CLASS__ . '::filterMessage'
                ),
                array(
                    'label' => __('Context', 'ipAdmin', FALSE),
                    'field' => 'context',
                    'preview' => __CLASS__ . '::filterContext'
                )
            )
        );
    }

    public static function filterMessage($value, $recordData)
    {
        $context = json_decode($recordData['context'], TRUE);

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
