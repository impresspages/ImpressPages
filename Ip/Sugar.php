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
    $site = \Ip\ServiceLocator::getSite();
    $site->setBlockContent($block, $content);
}

function ipAddJavascript($file, $stage = 1)
{
    $response = \Ip\ServiceLocator::getResponse();
    if (method_exists($response, 'addJavascript')) {
        $response->addJavascript($file, $stage);
    } else {
        ipLog('Core', 'Response method has no method addJavascript');
    }
}

function ipAddCss($file, $stage = 1)
{
    $response = \Ip\ServiceLocator::getResponse();
    if (method_exists($response, 'addCss')) {
        $response->addCss($file, $stage);
    } else {
        ipLog('Core', 'Response method has no method addCss');
    }

}

function ipLog($module, $message, $severity, $debugInfo = null)
{
    //TODOX
}

function ipJavascript()
{
    return \Ip\ServiceLocator::getResponse()->generateJavascript();
}

function ipHead()
{
    return \Ip\ServiceLocator::getResponse()->generateHead();
}

function ipSetLayout()
{
    $response = \Ip\ServiceLocator::getResponse();
    if (method_exists($response, 'setLayout')) {
        $response->setLayout($file, $stage);
    } else {
        ipLogNotice('Core', 'Response method has no method setLayout');
    }
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
