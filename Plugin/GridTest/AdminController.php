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
                    'label' => __('Name', 'GridTest', false),
                    'field' => 'name',
                ),
                array(
                    'type' => 'Select',
                    'label' => __('Age', 'GridTest', false),
                    'field' => 'age',
                    'values' => array(
                        array('young', __('Young', 'GridTest', false)),
                        array('old', __('Old', 'GridTest', false))
                    )
                ),
                array(
                    'type' => 'Checkbox',
                    'label' => __('In love', 'GridTest', false),
                    'showInList' => true,
                    'field' => 'inLove'
                ),
                array(
                    'type' => 'RepositoryFile',
                    'label' => __('CV', 'GridTest', false),
                    'showInList' => true,
                    'field' => 'cv'
                )

            )
        );
    }



}
