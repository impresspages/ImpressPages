<?php
/**
 * @package   ImpressPages
 *
 *
 */
namespace Plugin\GridTest;



class AdminController extends \Ip\GridController
{



    protected function config()
    {



        return array(
            'type' => 'table',
            'table' => 'grid_test',
            'sortField' => 'gridOrder',
            'createPosition' => 'top',
            'pageSize' => 3,
            'fields' => array(
                array(
                    'label' => __('Message', 'ipAdmin', false),
                    'field' => 'message',
                ),
//                array(
//                    'label' => __('Abbreviation', 'ipAdmin', false),
//                    'field' => 'd_short',
//                    'showInList' => true
//                ),
//                array(
//                    'type' => 'Checkbox',
//                    'label' => __('Visible', 'ipAdmin', false),
//                    'field' => 'visible'
//                ),
//                array(
//                    'label' => __('Url', 'ipAdmin', false),
//                    'field' => 'url',
//                    'showInList' => false,
//                    'validators' => array(
//                        'Required',
//                        array('Regex', '/^([^\/\\\])+$/', __('You can\'t use slash in URL.', 'ipAdmin', FALSE)),
//                        array('NotInArray', $languageUrls, __('Already taken', 'ipAdmin', FALSE) ),
//                    )
//                ),
//                array(
//                    'label' => __('RFC 4646 code', 'ipAdmin', false),
//                    'field' => 'code',
//                    'showInList' => false
//                ),
//                array(
//                    'type' => 'Select',
//                    'label' => __('Text direction', 'ipAdmin', false),
//                    'field' => 'text_direction',
//                    'showInList' => false,
//                    'values' => array(
//                        array('ltr', __('Left To Right', 'ipAdmin', false)),
//                        array('rtl', __('Right To Left', 'ipAdmin', false))
//                    )
//                ),
            )
        );
    }



}
