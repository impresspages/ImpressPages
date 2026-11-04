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
            'title' => __('Log', 'Ip-admin', false),
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

        return self::formatContext($context);
    }

    /**
     * Readable "key: value" lines instead of raw var_dump() output. Nested arrays are indented.
     */
    protected static function formatContext($context, $indent = '')
    {
        $lines = [];
        foreach ($context as $key => $value) {
            if (is_array($value)) {
                $lines[] = $indent . esc((string)$key) . ':' . (empty($value) ? ' (empty)' : '');
                if (!empty($value)) {
                    // non-breaking spaces, because the column collapses normal ones (white-space: pre-line)
                    $lines[] = self::formatContext($value, $indent . '&nbsp;&nbsp;&nbsp;&nbsp;');
                }
                continue;
            }
            if (is_object($value)) {
                $value = get_class($value);
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } elseif ($value === null) {
                $value = 'null';
            }
            $lines[] = $indent . esc((string)$key) . ': ' . esc((string)$value);
        }
        return implode("\n", $lines);
    }

    public function clear()
    {
        ipDb()->delete('log', []);
        return new \Ip\Response\Json(array('status' => 'success'));
    }
}
