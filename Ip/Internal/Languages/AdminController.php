<?php
/**
 * @package   ImpressPages
 *
 *
 */
namespace Ip\Internal\Languages;



class AdminController extends \Ip\Grid\Controller
{
    protected function config()
    {
        return array(
            'type' => 'table',
            'table' => 'language',
            'actions' => array(
                array(
                    'label' => __('Add', 'ipAdmin', false),
                    'class' => 'ipsCustomAdd'
                ),
                array(
                    'label' => __('Search', 'ipAdmin', false),
                    'class' => 'ipsSearch'
                )
            ),
            'fields' => array(
                array(
                    'label' => __('Title', 'ipAdmin', false),
                    'field' => 'd_long',
                ),
                array(
                    'label' => __('Abbreviation', 'ipAdmin', false),
                    'field' => 'd_short',
                    'showInList' => false
                ),
                array(
                    'label' => __('Visible', 'ipAdmin', false),
                    'field' => 'visible'
                ),
                array(
                    'label' => __('Url', 'ipAdmin', false),
                    'field' => 'url',
                    'showInList' => false

                    /*
                    //TODOX add URL validator
                    'regExpression' => '/^([^\/\\\])+$/',
                    'regExpressionError' => __('Incorrect URL. You can\'t use slash in URL.', 'ipAdmin')
                    */
                ),
                array(
                    'label' => __('RFC 4646 code', 'ipAdmin', false),
                    'field' => 'code',
                    'showInList' => false
                ),
                array(
                    'label' => __('Text direction', 'ipAdmin', false),
                    'field' => 'text_direction',
                    'showInList' => false
                    //TODOX add select
                ),
            )
        );
    }

}
