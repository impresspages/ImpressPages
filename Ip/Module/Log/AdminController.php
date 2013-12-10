<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Module\Log;



class AdminController extends \Ip\Grid1\Controller
{
    protected function config()
    {
        return array (
            'type' => 'table',
            'table' => DB_PREF . 'log',
            'fields' => array(
                array(
                    'label' => __('Time', 'ipAdmin', false),
                    'field' => 'time'
                ),
                array(
                    'label' => __('Message', 'ipAdmin', false),
                    'field' => 'message'
                ),
//                -        $element->setPreviewValueFilter(function($value, $info) {
//                        -                $context = json_decode($info['record']['context'], true);
//                        -
//                        -                $replace = array();
//                        -                foreach ($context as $key => $val) {
//                            -                    if (is_string($val) || is_numeric($val)) {
//                                -                        $replace['{' . $key . '}'] = '<em>' . $val . '</em>';
//                                -                    }
//-                }
//-
//-                return strtr($info['record']['message'], $replace);
//-            });


                array(
                    'label' => __('Context', 'ipAdmin', false),
                    'field' => 'context'
                )
//                $element->setPreviewValueFilter(function($value, $info) {
//                        -                $context = json_decode($info['record']['context'], true);
//                        -
//                        -                unset($context['exception']);
//-
//-                if (function_exists('ob_start')) {
//                            -                    ob_start();
//                            -                    var_dump($context);
//                            -                    return ob_get_clean();
//-                } else {
//                            -                    return var_export($context, true);
//-                }
//-            });


            )
        );
    }

}
