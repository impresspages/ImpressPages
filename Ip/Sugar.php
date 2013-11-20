<?php

/**
 * ImpressPages sugar methods
 */

function ipGetOption($option, $defaultValue = null)
{
    return \Ip\ServiceLocator::options()->getOption($option, $defaultValue);
}

/**
 * @return \Ip\Config
 */
function ipConfig()
{
    return \Ip\ServiceLocator::config();
}


function ipSetOption($option, $value)
{
    \Ip\ServiceLocator::options()->setOption($option, $value);
}

function ipGetBreadcrumb()
{
    return \Ip\ServiceLocator::content()->getBreadcrumb();
}

function ipGetZones()
{
    return \Ip\ServiceLocator::content()->getZones();
}

function ipGetCurrentZone()
{
    return \Ip\ServiceLocator::content()->getCurrentZone();
}

function ipGetZone($zoneName)
{
    return \Ip\ServiceLocator::content()->getZone($zoneName);
}

function ipGetCurrentLanguage()
{
    return \Ip\ServiceLocator::content()->getCurrentLanguage();
}

function ipGetLanguages()
{
    return \Ip\ServiceLocator::content()->getLanguages();
}

function ipGetCurrentPage()
{
    return \Ip\ServiceLocator::content()->getCurrentPage();
}

function ipSetBlockContent($block, $content)
{
    \Ip\ServiceLocator::content()->setBlockContent($block, $content);
}

function ipSetLayoutVariable($name, $value)
{
    $response = \Ip\ServiceLocator::response();
    if (method_exists($response, 'setLayoutVariable')) {
        $response->setLayoutVariable($name, $value);
    } else {
        ipLog('Core', 'Response method has no method setLayoutVariable');
    }
}

//TODOX remove
function ipAddJavascript($file, $stage = 1)
{
    $response = \Ip\ServiceLocator::response();
    if (method_exists($response, 'addJavascript')) {
        $response->addJavascript($file, $stage);
    }
}

function ipAddPluginAsset($plugin, $file, $attributes = array(), $priority = 1, $cacheFix = true)
{
    $response = \Ip\ServiceLocator::response();
    if (strtolower(substr($file, -3)) == '.js') { // todox: make more foolproof checking
        if (method_exists($response, 'addJavascript')) {
            $response->addJavascript(ipConfig()->pluginUrl($plugin . '/' . \Ip\Application::ASSET_DIR . '/' . $file), $attributes, $priority, $cacheFix);
        }
    } else { // todox: make more foolproof checking
        if (method_exists($response, 'addCss')) {
            $response->addCss(ipConfig()->pluginUrl($plugin . '/' . \Ip\Application::ASSET_DIR . '/' . $file), $attributes, $priority, $cacheFix);
        }
    }
}

function ipAddThemeAsset($file, $attributes = array(), $priority = 1, $cacheFix = true)
{
    $response = \Ip\ServiceLocator::response();
    if (strtolower(substr($file, -3)) == '.js') { // todox: make more foolproof checking
        if (method_exists($response, 'addJavascript')) {
            $response->addJavascript(ipConfig()->themeUrl(\Ip\Application::ASSET_DIR . '/' . $file), $attributes, $priority, $cacheFix);
        }
    } else { // todox: make more foolproof checking
        if (method_exists($response, 'addCss')) {
            $response->addCss(ipConfig()->themeUrl(\Ip\Application::ASSET_DIR . '/' . $file), $attributes, $priority, $cacheFix);
        }
    }
}

function ipAddJQuery()
{
    $response = \Ip\ServiceLocator::response();
    if (method_exists($response, 'addJavascript')) {
        $response->addJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/jquery.js'));
    }
}


function ipAddJavascriptVariable($name, $value)
{
    $response = \Ip\ServiceLocator::response();
    if (method_exists($response, 'addJavascriptVariable')) {
        $response->addJavascriptVariable($name, $value);
    } else {
        ipLog('Core', 'Response method has no method addJavascriptVariable');
    }
}



function ipAddCss($file, $stage = 1)
{
    $response = \Ip\ServiceLocator::response();
    if (method_exists($response, 'addCss')) {
        $response->addCss($file, $stage);
    } else {
        ipLog('Core', 'Response method has no method addCss');
    }

}

function ipLog($module, $message, $severity = 0, $debugInfo = null)
{
    //TODOX
}

/**
 * @param bool $print false - return instead of print
 * @return string
 */
function ipPrintJavascript($print = true)
{
    $script = \Ip\ServiceLocator::response()->generateJavascript();
    if ($print) {
        echo $script;
        return '';
    } else {
        return $script;
    }
}

/**
 * @param bool $print false - return instead of print
 * @return string
 */
function ipPrintHead($print = true)
{
    $head = \Ip\ServiceLocator::response()->generateHead();
    if ($print) {
        echo $head;
        return '';
    } else {
        return $head;
    }
}

function ipSetLayout($file)
{
    $response = \Ip\ServiceLocator::response();
    if (method_exists($response, 'setLayout')) {
        $response->setLayout($file);
    } else {
        ipLog('Core', 'Response method has no method setLayout');
    }
}

function ipGetLayout()
{
    $response = \Ip\ServiceLocator::response();
    if (method_exists($response, 'getLayout')) {
        $response->getLayout();
    } else {
        ipLog('Core', 'Response method has no method getLayout');
    }
}

function ipEsc($text)
{
    return htmlspecialchars($text, ENT_QUOTES);
}

/**
 * @param $block
 * @return \Ip\Block
 */
function ipBlock($block)
{
    return \Ip\ServiceLocator::content()->generateBlock($block);
}

/**
 * @param $slot
 * @return string
 */
function ipSlot($slot, $params = array())
{
    return \Ip\ServiceLocator::content()->generateSlot($slot, $params);
}


function ipIsManagementState()
{
    return \Ip\ServiceLocator::content()->isManagementState();
}

function ipRequest()
{
    return \Ip\ServiceLocator::request();
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

/**
 * @return \Ip\Dispatcher
 */
function ipDispatcher()
{
    return \Ip\ServiceLocator::dispatcher();
}

/**
 * @return \Ip\Db
 */
function ipDb()
{
    return \Ip\ServiceLocator::db();
}

//function _n($singular, $plural, $number, $domain)
//{
//    return \Ip\Translator::translatePlural($singular, $plural, $number, $domain);
//}
