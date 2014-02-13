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
                    'label' => __('Name', 'ipAdmin', false),
                    'field' => 'message',
                ),
                array(
                    'type' => 'Select',
                    'label' => __('Age', 'ipAdmin', false),
                    'field' => 'age',
                    'values' => array(
                        array('young', __('Young', 'ipAdmin', false)),
                        array('old', __('Old', 'ipAdmin', false))
                    )
                ),
                array(
                    'type' => 'Checkbox',
                    'label' => __('In love', 'ipAdmin', false),
                    'showInList' => true,
                    'field' => 'inLove'
                )

            )
        );
    }



}
