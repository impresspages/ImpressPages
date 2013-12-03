<?php

/**
 * ImpressPages sugar methods
 */

function ipGetOption($option, $defaultValue = null)
{
    return \Ip\ServiceLocator::options()->getOption($option, $defaultValue);
}


function ipGetOptionLang($option, $languageId, $defaultValue = null)
{
    return \Ip\ServiceLocator::options()->getOptionLang($option, $languageId, $defaultValue);
}


function ipSetOption($option, $value)
{
    \Ip\ServiceLocator::options()->setOption($option, $value);
}

function ipSetOptionLang($option, $value, $languageId)
{
    \Ip\ServiceLocator::options()->setOptionLang($option, $languageId, $value);
}

function ipRemoveOption($option)
{
    return \Ip\ServiceLocator::options()->removeOption($option);
}

function ipRemoveOptionLang($option, $languageId)
{
    return \Ip\ServiceLocator::options()->getOptionLang($option, $languageId);
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

//TODOX rename to ipAddJs
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
            $response->addJavascript(ipFileUrl('Plugin/' . $plugin . '/' . \Ip\Application::ASSET_DIR . '/' . $file), $attributes, $priority, $cacheFix);
        }
    } else { // todox: make more foolproof checking
        if (method_exists($response, 'addCss')) {
            $response->addCss(ipFileUrl('Plugin/' . $plugin . '/' . \Ip\Application::ASSET_DIR . '/' . $file), $attributes, $priority, $cacheFix);
        }
    }
}

function ipAddPluginJs($plugin, $file, $attributes = array(), $priority = 1, $cacheFix = true)
{
    $response = \Ip\ServiceLocator::response();
    if (method_exists($response, 'addJavascript')) {
        $response->addJavascript(ipFileUrl('Plugin/' . $plugin . '/' . \Ip\Application::ASSET_DIR . '/' . $file), $attributes, $priority, $cacheFix);
    }
}

function ipAddPluginCss($plugin, $file, $attributes = array(), $priority = 1, $cacheFix = true)
{
    $response = \Ip\ServiceLocator::response();
    if (method_exists($response, 'addCss')) {
        $response->addCss(ipConfig()->pluginUrl($plugin . '/' . \Ip\Application::ASSET_DIR . '/' . $file), $attributes, $priority, $cacheFix);
    }
}


function ipAddThemeAsset($file, $attributes = array(), $priority = 1, $cacheFix = true)
{
    $response = \Ip\ServiceLocator::response();
    if (strtolower(substr($file, -3)) == '.js') { // todox: make more foolproof checking
        if (method_exists($response, 'addJavascript')) {
            $response->addJavascript(ipThemeUrl(\Ip\Application::ASSET_DIR . '/' . $file), $attributes, $priority, $cacheFix);
        }
    } else { // todox: make more foolproof checking
        if (method_exists($response, 'addCss')) {
            $response->addCss(ipThemeUrl(\Ip\Application::ASSET_DIR . '/' . $file), $attributes, $priority, $cacheFix);
        }
    }
}

function ipAddJQuery()
{
    $response = \Ip\ServiceLocator::response();
    if (method_exists($response, 'addJavascript')) {
        $response->addJavascript(ipFileUrl('Ip/Module/Assets/assets/js/jquery.js'));
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
//TODOX remove $print parameters. People can use long syntax if they want the response
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

/**
 * @param string $string
 * @param string $esc html|attr|textarea|url|urlRaw|raw or false
 */
function esc($string, $esc = 'html')
{
    if (!$esc) {
        return $string;
    }

    if ('html' == $esc) {
        return escHtml($string);
    } elseif ('attr' == $esc) {
        return escAttr($string);
    } elseif ('url' == $esc) {
        return escUrl($string);
    } elseif ('urlRaw' == $esc || 'urlraw' == $esc) {
        return escUrlRaw($string);
    } elseif ('textarea' == $string) {
        return escTextarea($string);
    }

    throw new \Ip\CoreException('Unknown escape method: {$esc}');
}


function escHtml($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function escTextarea($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function escAttr($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function escUrl($url)
{
    // TODOX implement
    throw new \Ip\CoreException('Not implemented yet.');
}

function escUrlRaw($url)
{
    // TODOX implement
    throw new \Ip\CoreException('Not implemented yet.');
}

function __($text, $domain, $esc = 'html')
{
    return esc(\Ip\Translator::translate($text, $domain), $esc);
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


function ipFile($path)
{
    static $basePath = '';
    if (!$basePath) {
        $basePath = ipConfig()->getRaw('BASE_DIR') . '/';
    }

    $overrides = ipConfig()->getRaw('FILE_OVERRIDES');
    if ($overrides) {
        foreach ($overrides as $prefix => $newPath) {
            if (strpos($path, $prefix) === 0) {
                return substr_replace($path, $newPath, 0, strlen($prefix));
            }
        }
    }

    return $basePath . $path;
}

function ipFileUrl($path)
{
    $overrides = ipConfig()->getRaw('URL_OVERRIDES');
    if ($overrides) {
        foreach ($overrides as $prefix => $newPath) {
            if (strpos($path, $prefix) === 0) {
                return substr_replace($path, $newPath, 0, strlen($prefix));
            }
        }
    }

    return ipConfig()->baseUrl() . $path;
}

function ipActionUrl($query)
{
    return ipConfig()->baseUrl() . '?' . http_build_query($query);
}

function ipThemeUrl($path)
{
    return ipFileUrl('Theme/' . ipConfig()->theme() . '/' . $path);
}

function ipThemeFile($path)
{
    return ipFile('Theme/' . ipConfig()->theme() . '/' . $path);
}

function ipHomeUrl()
{
    return \Ip\Internal\Deprecated\Url::generate();
}
