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

function ipGetBreadcrumb()
{
    return \Ip\ServiceLocator::getContent()->getBreadcrumb();
}

function ipGetZones()
{
    return \Ip\ServiceLocator::getContent()->getZones();
}

function ipGetCurrentZone()
{
    return \Ip\ServiceLocator::getContent()->getCurrentZone();
}

function ipGetZone($zoneName)
{
    return \Ip\ServiceLocator::getContent()->getZone($zoneName);
}

function ipGetCurrentLanguage()
{
    return \Ip\ServiceLocator::getContent()->getCurrentLanguage();
}

function ipGetLanguages()
{
    return \Ip\ServiceLocator::getContent()->getLanguages();
}

function ipGetCurrentPage()
{
    return \Ip\ServiceLocator::getContent()->getCurrentPage();
}

function ipSetBlockContent($block, $content)
{
    \Ip\ServiceLocator::getContent()->setBlockContent($block, $content);
}

function ipSetLayoutVariable($name, $value)
{
    $response = \Ip\ServiceLocator::getResponse();
    if (method_exists($response, 'setLayoutVariable')) {
        $response->setLayoutVariable($name, $value);
    } else {
        ipLog('Core', 'Response method has no method setLayoutVariable');
    }
}

//TODOX remove
function ipAddJavascript($file, $stage = 1)
{
    $response = \Ip\ServiceLocator::getResponse();
    if (method_exists($response, 'addJavascript')) {
        $response->addJavascript($file, $stage);
    }
}

function ipAddPluginAsset($plugin, $file, $priority = 1)
{
    $response = \Ip\ServiceLocator::getResponse();
    if (method_exists($response, 'addJavascript')) {
        $response->addJavascript(\Ip\Config::pluginUrl($plugin . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $file), $priority);
    }
}

function ipAddThemeAsset($file, $priority = 1)
{
    $response = \Ip\ServiceLocator::getResponse();
    if (strtolower(substr($file, -3)) == '.js') {
        if (method_exists($response, 'addJavascript')) {
            $response->addJavascript(\Ip\Config::themeUrl('assets' . DIRECTORY_SEPARATOR . $file), $priority);
        }
    } else {
        if (method_exists($response, 'addJavascript')) {
            $response->addCss(\Ip\Config::themeUrl('assets' . DIRECTORY_SEPARATOR . $file), $priority);
        }
    }
}

function ipAddJQuery()
{
    $response = \Ip\ServiceLocator::getResponse();
    if (method_exists($response, 'addJavascript')) {
        $response->addJavascript(\Ip\Config::coreModuleUrl('Assets/assets/js/jquery.js'));
    }
}


function ipAddJavascriptVariable($name, $value)
{
    $response = \Ip\ServiceLocator::getResponse();
    if (method_exists($response, 'addJavascriptVariable')) {
        $response->addJavascriptVariable($name, $value);
    } else {
        ipLog('Core', 'Response method has no method addJavascriptVariable');
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

function ipPrintJavascript($return = false)
{
    $script = \Ip\ServiceLocator::getResponse()->generateJavascript();
    if ($return) {
        return $script;
    } else {
        echo $script;
        return '';
    }
}

function ipPrintHead($return = false)
{
    $head = \Ip\ServiceLocator::getResponse()->generateHead();
    if ($return) {
        return $head;
    } else {
        echo $head;
        return '';
    }
}

function ipSetLayout($file)
{
    $response = \Ip\ServiceLocator::getResponse();
    if (method_exists($response, 'setLayout')) {
        $response->setLayout($file);
    } else {
        ipLog('Core', 'Response method has no method setLayout');
    }
}

function ipGetLayout()
{
    $response = \Ip\ServiceLocator::getResponse();
    if (method_exists($response, 'getLayout')) {
        $response->getLayout();
    } else {
        ipLog('Core', 'Response method has no method getLayout');
    }
}

/**
 * @param $block
 * @return \Ip\Block
 */
function ipBlock($block)
{
    return \Ip\ServiceLocator::getContent()->generateBlock($block);
}

/**
 * @param $slot
 * @return string
 */
function ipSlot($slot, $params = array())
{
    return \Ip\ServiceLocator::getContent()->generateSlot($slot, $params);
}


function ipIsManagementState()
{
    return \Ip\ServiceLocator::getContent()->isManagementState();
}

function ipGetRequest()
{
    return \Ip\ServiceLocator::getRequest();
}

function __($text, $domain)
{
    return htmlentities(\Ip\Translator::translate($text, $domain), (ENT_COMPAT), 'UTF-8');
}

function _e($text, $domain)
{
    echo __($text, $domain);
}

function _s($text, $domain)
{
    return \Ip\Translator::translate($text, $domain);
}

//function _n($singular, $plural, $number, $domain)
//{
//    return \Ip\Translator::translatePlural($singular, $plural, $number, $domain);
//}
