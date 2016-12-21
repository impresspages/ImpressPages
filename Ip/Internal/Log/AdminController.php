<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Log;


class AdminController extends \Ip\GridController
{
    public function index()
    {
        ipAddJs('assets/log.js');
        ipAddJsVariable('clearConfirmTranslation', __('Are you sure you want to delete all log records?', 'Ip-admin', false));
        return parent::index();
    }
    protected function config()
    {
        return array(
            'type' => 'table',
            'table' => 'log',
            'allowCreate' => false,
            'allowUpdate' => false,
            'allowDelete' => false,
            'orderBy' => '`id` desc',
            'actions' => array(
                array(
                    'label' => __('Clear all', 'Ip-admin', false),
                    'class' => 'ipsClearAll'
                )
            ),
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

        $replace = [];
        if (is_array($context)) {
            foreach ($context as $key => $val) {
                if (is_string($val) || is_numeric($val)) {
                    $replace['{' . $key . '}'] = '<em>' . esc($val) . '</em>';
                }
            }
        }

        $message = esc($recordData['message']);

        return strtr($message, $replace);
    }

    public static function previewContext($value, $recordData)
    {
        $context = json_decode($recordData['context'], true);

        if (!is_array($context)) {
            $context = array($context);
        }

        unset($context['exception']);

        array_walk_recursive($context, function(&$v) {
                if (is_object($v)) {
                    $v = serialize($v);
                }
                $v = htmlspecialchars($v);
            }
        );


        if (function_exists('ob_start')) {
            ob_start();
            var_dump($context);
            return ob_get_clean();
        } else {
            return var_export($context, true);
        }
    }

    public function clear()
    {
        ipDb()->delete('log', []);
        return new \Ip\Response\Json(array('status' => 'success'));
    }
}
