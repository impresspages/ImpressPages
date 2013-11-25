<?php

/**
 * ImpressPages sugar methods
 */

function ipGetOption($option, $defaultValue = null)
{
    return \Ip\ServiceLocator::options()->getOption($option, $defaultValue);
}


function ipSetOption($option, $value)
{
    \Ip\ServiceLocator::options()->setOption($option, $value);
}

/**
 * @return \Ip\Config
 */
function ipConfig()
{
    return \Ip\ServiceLocator::config();
}



/**
 * @return \Ip\Content
 */
function ipContent()
{
    return \Ip\ServiceLocator::content();
}

function ipSetLayoutVariable($name, $value)
{
    $response = \Ip\ServiceLocator::response();
    if (method_exists($response, 'setLayoutVariable')) {
        $response->setLayoutVariable($name, $value);
    } else {
        ipLog()->error('Response.cantSetLayoutVariable: Response method has no method setLayoutVariable', array('response' => $response));
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
        ipLog()->error('Response.cantAddJavascriptVariable: Response method has no method addJavascriptVariable', array('response' => $response));
    }
}



function ipAddCss($file, $stage = 1)
{
    $response = \Ip\ServiceLocator::response();
    if (method_exists($response, 'addCss')) {
        $response->addCss($file, $stage);
    } else {
        ipLog()->error('Response.cantAddCss: Response method has no addCss method', array('response' => $response));
    }

}

function ipLog()
{
    return \Ip\ServiceLocator::log();
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
        ipLog()->error('Response.cantSetLayout: Response has no setLayout method', array('response' => $response));
    }
}

/**
 * @return \Ip\Response | \Ip\Response\Layout
 */
function ipResponse()
{
    return \Ip\ServiceLocator::response();
}

function ipGetLayout()
{
    $response = \Ip\ServiceLocator::response();
    if (method_exists($response, 'getLayout')) {
        $response->getLayout();
    } else {
        ipLog()->error('Response.cantGetLayout: Response method has no method getLayout', array('response' => $response));
    }
}

/**
 * @param string $string
 * @param string $esc html|attr|textarea|js|url|urlRaw|raw or false
 */
function esc($string, $esc = 'html')
{
    if (!$esc) {
        return $string;
    }

    return htmlspecialchars($string, ENT_QUOTES);
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

function escHtml($string) {}
function escTextarea($value) {}
function escAttr($value) {}
function escJs($string) {}
function escUrl($url) {}
function escUrlRaw($url){}

function __($text, $domain, $esc = 'html')
{
    return htmlentities(\Ip\Translator::translate($text, $domain), (ENT_COMPAT), 'UTF-8');
}

function _e($text, $domain, $esc = 'html')
{
    echo __($text, $domain, $esc);
}

//TODOX ask Algimantas if this is still used
//function _n($singular, $plural, $number, $domain)
//{
//    return \Ip\Translator::translatePlural($singular, $plural, $number, $domain);
//}
