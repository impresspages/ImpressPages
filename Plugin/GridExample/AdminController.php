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
            'title' => __('Person list', 'ipAdmin', FALSE),
            'table' => 'person',
//            'sortField' => 'gridOrder',
//            'createPosition' => 'top',
//            'pageSize' => 3,
            'fields' => array(
                array(
                    'label' => __('Name', 'GridTest', FALSE),
                    'field' => 'name',
                ),
                array(
                    'type' => 'Select',
                    'label' => __('Age', 'GridTest', FALSE),
                    'field' => 'age',
                    'values' => array(
                        array('young', __('Young', 'GridTest', FALSE)),
                        array('old', __('Old', 'GridTest', FALSE))
                    )
                ),
                array(
                    'type' => 'Checkbox',
                    'label' => __('In love', 'GridTest', FALSE),
                    'showInList' => true,
                    'field' => 'inLove'
                ),
                array(
                    'type' => 'RepositoryFile',
                    'label' => __('CV', 'GridTest', FALSE),
                    'showInList' => true,
                    'field' => 'cv'
                )

            )
        );
    }



}
