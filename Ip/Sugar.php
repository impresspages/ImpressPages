<?php

/**
 * ImpressPages sugar methods
 */

function ipGetOption($option, $defaultValue = null)
{
    return \Ip\ServiceLocator::getOptions()->getOption($option, $defaultValue);
}



function ipSetOption($option, $value)
{
    \Ip\ServiceLocator::getOptions()->setOption($option, $value);
}

function ipGetCurrentZone()
{
    return \Ip\ServiceLocator::getContent()->getCurrentZone();
}

function ipGetCurrentLanguage()
{
    return \Ip\ServiceLocator::getContent()->getCurrentLanguage();
}

function ipGetCurrentPage()
{
    return \Ip\ServiceLocator::getContent()->getCurrentPage();
}


function ipSetBlockContent($block, $content)
{
//    $content = \Ip\ServiceLocator::getContent();
//    $content->setBlockContent();
}


function __($text, $domain)
{
    return \Ip\Translator::translate($text, $domain);
}

function _n($singular, $plural, $number, $domain)
{
    return \Ip\Translator::translatePlural($singular, $plural, $number, $domain);
}

function _x($text, $context, $domain)
{
    return $text;
}

function _nx($single, $plural, $number, $context, $domain)
{

}
