<?php

/**
 * ImpressPages sugar methods
 */


/**
 * Get CMS application object.
 * @return \Ip\Application
 */
function ipApplication()
{
    return \Ip\ServiceLocator::application();
}

/**
 * Get security token string. Used to prevent XSRF attacks.
 *
 * Security token is a long random string generated for currently browsing user.
 * @return string
 */
function ipSecurityToken()
{
    return ipApplication()->getSecurityToken();
}

/**
 * Get CMS option value.
 * Options can be viewed or changed using administration pages. You can use this function to get your plugin settings.
 * @param $option
 * @param null $defaultValue
 * @return string
 */
function ipGetOption($option, $defaultValue = null)
{
    return \Ip\ServiceLocator::options()->getOption($option, $defaultValue);
}

/**
 * Get language specific CMS option value.
 * @param $option
 * @param $languageId
 * @param null $defaultValue
 * @return string
*/

function ipGetOptionLang($option, $languageId, $defaultValue = null)
{
    return \Ip\ServiceLocator::options()->getOptionLang($option, $languageId, $defaultValue);
}

/**
 * Set CMS option value.
 * Options can be viewed or changed using administration pages. You can use this function to set your plugin settings.
 * @param $option
 * @param $value
 */
function ipSetOption($option, $value)
{
    \Ip\ServiceLocator::options()->setOption($option, $value);
}

/**
 *  Set language specific CMS option value.
 * @param $option
 * @param $value
 * @param $languageId
 */
function ipSetOptionLang($option, $value, $languageId)
{
    \Ip\ServiceLocator::options()->setOptionLang($option, $languageId, $value);
}

/**
 * Remove CMS option.
 * Options can be viewed or changed using administration pages.
 * @param $option
 */
function ipRemoveOption($option)
{
    return \Ip\ServiceLocator::options()->removeOption($option);
}

/**
 *  Remove language specific CMS option value.
 * @param $option
 * @param $value
 * @param $languageId
 */
function ipRemoveOptionLang($option, $languageId)
{
    return \Ip\ServiceLocator::options()->getOptionLang($option, $languageId);
}


/**
 * Get website configuration object.
 * @return \Ip\Config
 */
function ipConfig()
{
    return \Ip\ServiceLocator::config();
}



/**
 * Get CMS content object.
 * Use this object to access zones, pages and languages.
 * @return \Ip\Content
 */
function ipContent()
{
    return \Ip\ServiceLocator::content();
}

/**
 * Use this object to get information about current page, language, zone.
 *
 * @return \Ip\CurrentPage
 */
function ipCurrentPage()
{
    return \Ip\ServiceLocator::currentPage();
}

/**
 * Add JavaScript file to a web page.
 * After adding all JavaScript files, use ipJs() function to generate JavaScript links HTML code.
 * @param $file JavaScript file
 * @param array $attributes for example array('id' => 'example')
 * @param int $priority
 */
function ipAddJs($file, $attributes = null, $priority = 50)
{
    if (preg_match('%(https?:)?//%', $file)) {
        $absoluteUrl = $file;
    } else {
        if (preg_match('%^(Plugin|Theme|file|Ip)/%', $file)) {
            $relativePath = $file;
        } else {
            $relativePath = ipRelativeDir(1) . $file;
        }

        $absoluteUrl = ipFileUrl($relativePath);
    }

    \Ip\ServiceLocator::pageAssets()->addJavascript($absoluteUrl, $attributes, $priority);
}

/**
 * Pass PHP variable to JavaScript code.
 * @param $name
 * @param $value
 */
function ipAddJsVariable($name, $value)
{
    \Ip\ServiceLocator::pageAssets()->addJavascriptVariable($name, $value);
}

/**
 * Add CSS file from your plugin or theme
 * After adding all CSS files, use ipHead() function to generate HTML head.
 * @param $file
 * @param array $attributes for example array('id' => 'example')
 * @param int $priority
 */
function ipAddCss($file, $attributes = null, $priority = 50)
{
    if (preg_match('%(https?:)?//%', $file)) {
        $absoluteUrl = $file;
    } else {
        if (preg_match('%^(Plugin|Theme|file|Ip)/%', $file)) {
            $relativePath = $file;
        } else {
            $relativePath = ipRelativeDir(1) . $file;
        }

        $absoluteUrl = ipFileUrl($relativePath);
    }

    \Ip\ServiceLocator::pageAssets()->addCss($absoluteUrl, $attributes, $priority);
}

/**
 * Return CMS log object.
 * Use this object to create and access log records.
 * @return \Psr\Log\LoggerInterface
 */
function ipLog()
{
    return \Ip\ServiceLocator::log();
}

/**
 * Generate HTML code which loads JavaScript files added by ipAddJs() function.
 * @return mixed
 */

function ipJs()
{
    return \Ip\ServiceLocator::pageAssets()->generateJavascript();
}

/**
 * Generate HTML head.
 * @return mixed
 */
function ipHead()
{
    return \Ip\ServiceLocator::pageAssets()->generateHead();
}

/**
 * Set HTML layout file.
 * @param $file
 */

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
 * Get CMS response object.
 * @return \Ip\Response | \Ip\Response\Layout
 */
function ipResponse()
{
    return \Ip\ServiceLocator::response();
}


function _ipPageStart(\Ip\Page $page)
{
    ipCurrentPage()->_set('page', $page);

    ipEvent('_ipPageStart', array('page' => $page));
}

/**
 * Get current HTML layout.
 */
function ipGetLayout()
{
    $response = \Ip\ServiceLocator::response();
    if (method_exists($response, 'getLayout')) {
        return $response->getLayout();
    } else {
        ipLog()->error('Response.cantGetLayout: Response method has no method getLayout', array('response' => $response));
    }
}

/**
 * Get CMS block object.
 * @param $block
 * @return \Ip\Block
 */
function ipBlock($block)
{
    return \Ip\ServiceLocator::content()->generateBlock($block);
}


/**
 * Get CMS slot object.
 * @param $slot
 * @param array $params
 */
function ipSlot($slot, $params = array())
{
    return \Ip\ServiceLocator::slots()->generateSlot($slot, $params);
}

/**
 * Checks if the website is opened in administration mode.
 * @return bool
 */

function ipIsManagementState()
{
    return \Ip\Internal\Content\Service::isManagementMode();
}

/**
 * Get HTTP request data for your plugins.
 * @return \Ip\Request
 */

function ipRequest()
{
    return \Ip\ServiceLocator::request();
}

/**
 * Create an event.
 * @return \Ip\Dispatcher
 */

function ipEvent($event, $data = array())
{
    return \Ip\ServiceLocator::dispatcher()->event($event, $data);
}

/**
 * Filter data.
 * @param $event filter name
 * @param $value
 * @param array $data
 * @return mixed filtered data
 */
function ipFilter($event, $value, $data = array())
{
    return \Ip\ServiceLocator::dispatcher()->filter($event, $value, $data);
}

/**
 * Create a job.
 * @param $eventName
 * @param array $data
 * @return mixed|null
 */
function ipJob($eventName, $data = array())
{
    return \Ip\ServiceLocator::dispatcher()->job($eventName, $data);
}


/**
 * Returns an object, which provides plugin developers with methods for connecting to database, executing SQL queries and fetching results.
 * @return \Ip\Db
 */
function ipDb()
{
    return \Ip\ServiceLocator::db();
}

/**
 * Get escaped string.
 * @param string $string
 * @param string $esc html|attr|textarea|url|urlRaw|raw or false
 * @return string escaped string
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
    } elseif ('textarea' == $string) {
        return escTextarea($string);
    }

    throw new \Ip\Exception('Unknown escape method: {$esc}');
}


/**
 * Get escaped HTML string
 * @param $string
 * @return string escaped string
 */
function escHtml($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Get escaped text area content.
 * @param $value
 * @return string escaped string
 */
function escTextarea($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Get escaped HTML attribute.
 * @param $value
 * @return string escaped string
 */

function escAttr($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Translate and escape string.
 * @param $text original value in English
 * @param $domain context, e.g. plugin name
 * @param string $esc escape type. Available values: false, 'html', 'attr', 'textarea'
 * @return string translated string or original string if no translation exists
 */
function __($text, $domain, $esc = 'html')
{
    return esc(\Ip\ServiceLocator::translator()->translate($text, $domain), $esc);
}

function _e($text, $domain, $esc = 'html')
{
    echo __($text, $domain, $esc);
}

if (!function_exists('ipFile')) {
    /**
     * Gets absolute CMS file path
     * @param $path
     * @return mixed|string
     */
    function ipFile($path)
    {
        global $ipFile_baseDir, $ipFile_overrides; // Optimization: caching these values speeds things up a lot

        if (!$ipFile_baseDir) {
            $ipFile_baseDir = ipConfig()->getRaw('BASE_DIR');
            $ipFile_overrides = ipConfig()->getRaw('FILE_OVERRIDES');
        }

        if ($ipFile_overrides) {
            foreach ($ipFile_overrides as $prefix => $newPath) {
                if (strpos($path, $prefix) === 0) {
                    return substr_replace($path, $newPath, 0, strlen($prefix));
                }
            }
        }

        return $ipFile_baseDir . '/' . $path;
    }
}

if (!function_exists('ipFileUrl')) {
    /**
     * Gets URL by a file name
     * @param $path
     * @return mixed|string
     */
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
}

/**
 * Generate URL-encoded query string
 * @param $query associative (or indexed) array
 * @return string URL string
 */
function ipActionUrl($query)
{
    return ipConfig()->baseUrl() . '?' . http_build_query($query);
}

/**
 * Get url for current theme folder.
 * @param $path
 * @return mixed|string
 */
function ipThemeUrl($path)
{
    return ipFileUrl('Theme/' . ipConfig()->theme() . '/' . $path);
}

/**
 * Gets file path for current theme folder.
 * @param $path
 * @return mixed|string
 */

function ipThemeFile($path)
{
    return ipFile('Theme/' . ipConfig()->theme() . '/' . $path);
}

/**
 * Get homepage URL.
 * @return string
 */
function ipHomeUrl()
{
    $homeUrl = ipConfig()->baseUrl();
    if (ipConfig()->getRaw('NO_REWRITES')) {
        $homeUrl .= 'index.php/';
    }

    if (ipGetOption('Config.multilingual')) {
        $homeUrl .= urlencode(ipContent()->getCurrentLanguage()->getUrl()) . '/';
    }

    return $homeUrl;
}

/**
 * Generate widget HTML.
 * @param $widgetName
 * @param array $data
 * @param null $skin
 * @return string
 */
function ipRenderWidget($widgetName, $data = array(), $skin = null)
{
    $answer = \Ip\Internal\Content\Model::generateWidgetPreviewFromStaticData($widgetName, $data, $skin);
    return $answer;
}

/**
 * Get formatted currency string.
 * @param $price
 * @param $currency
 * @param $context
 * @param null $languageId
 * @return string
 */

function ipFormatPrice($price, $currency, $context, $languageId = null)
{
    return \Ip\Internal\FormatHelper::formatPrice($price, $currency, $context, $languageId);
}

/**
 * Get formatted date string.
 * @param $unixTimestamp
 * @param $context
 * @param null $languageId
 * @return bool|mixed|null|string
 */
function ipFormatDate($unixTimestamp, $context, $languageId = null)
{
    return \Ip\Internal\FormatHelper::formatDate($unixTimestamp, $context, $languageId);
}

/**
 * Gets formatted time string.
 * @param $unixTimestamp
 * @param $context
 * @param null $languageId
 * @return bool|mixed|null|string
 */
function ipFormatTime($unixTimestamp, $context, $languageId = null)
{
    return \Ip\Internal\FormatHelper::formatTime($unixTimestamp, $context, $languageId);
}

/**
 * Get formatted date-time string.
 * @param $unixTimestamp
 * @param $context
 * @param null $languageId
 * @return bool|mixed|null|string
 */
function ipFormatDateTime($unixTimestamp, $context, $languageId = null)
{
    return \Ip\Internal\FormatHelper::formatDateTime($unixTimestamp, $context, $languageId);
}

/**
 * Get a theme option value.
 * Theme options ar used for changing theme design. These options can be managed using administration page.
 * @param $name
 * @param null $default
 * @return string
 */

function ipGetThemeOption($name, $default = null)
{
    $themeService = \Ip\Internal\Design\Service::instance();
    return $themeService->getThemeOption($name, $default);
}

/**
 * Get HTML attributes for <html> tag.
 * @param null $doctype
 * @return string
 */
function ipHtmlAttributes($doctype = null)
{
    $content = \Ip\ServiceLocator::content();
    if ($doctype === null) {
        $doctypeConstant = ipConfig()->getRaw('DEFAULT_DOCTYPE');
        $doctype = constant('\Ip\Response\Layout::' . $doctypeConstant);
    }
    switch ($doctype) {
        case \Ip\Response\Layout::DOCTYPE_XHTML1_STRICT:
        case \Ip\Response\Layout::DOCTYPE_XHTML1_TRANSITIONAL:
        case \Ip\Response\Layout::DOCTYPE_XHTML1_FRAMESET:
            $lang = $content->getCurrentLanguage()->getCode();
            $answer = ' xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$lang.'" lang="'.$lang.'"';
            break;
        case \Ip\Response\Layout::DOCTYPE_HTML4_STRICT:
        case \Ip\Response\Layout::DOCTYPE_HTML4_TRANSITIONAL:
        case \Ip\Response\Layout::DOCTYPE_HTML4_FRAMESET:
        default:
            $answer = '';
            break;
        case \Ip\Response\Layout::DOCTYPE_HTML5:
            $lang = $content->getCurrentLanguage()->getCode();
            $answer = ' lang="'.$lang.'"';
            break;
    }
    return  $answer;

}

/**
 * Get HTML doc type.
 * @param null $doctype
 * @return string
 * @throws Exception
 */

function ipDoctypeDeclaration($doctype = null)
{
    if ($doctype === null) {
        $doctypeConstant = ipConfig()->getRaw('DEFAULT_DOCTYPE');
        $doctype = constant('\Ip\Response\Layout::' . $doctypeConstant);
    }
    switch ($doctype) {
        case \Ip\Response\Layout::DOCTYPE_XHTML1_STRICT:
            $answer = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
            break;
        case \Ip\Response\Layout::DOCTYPE_XHTML1_TRANSITIONAL:
            $answer = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
            break;
        case \Ip\Response\Layout::DOCTYPE_XHTML1_FRAMESET:
            $answer = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
            break;
        case \Ip\Response\Layout::DOCTYPE_HTML4_STRICT:
            $answer = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
            break;
        case \Ip\Response\Layout::DOCTYPE_HTML4_TRANSITIONAL:
            $answer = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
            break;
        case \Ip\Response\Layout::DOCTYPE_HTML4_FRAMESET:
            $answer = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
            break;
        case \Ip\Response\Layout::DOCTYPE_HTML5:
            $answer = '<!DOCTYPE html>';
            break;
        default:
            throw new Exception('Unknown doctype: '.$doctype, Exception::VIEW);
    }
    return $answer;
}

/**
 * Get SQL table name by adding CMS database prefix.
 * @param $table
 * @param null $as
 * @return string
 */
function ipTable($table, $as = null)
{
    $answer = '`' . ipConfig()->tablePrefix() . $table . '`';
    if ($as != false) {
        if ($as !== null) {
            $answer .= 'as ' . $as;
        } else {
            $answer .= 'as ' . $table;
        }
    }
    return $answer;
}

/**
 * Check if user has right to execute administrative action on plugin
 * @param $plugin
 * @param null $action
 * @return bool true if user has permission to do the action
 */
function ipAdminPermission($plugin, $action = NULL)
{
    return \Ip\ServiceLocator::permissions()->isAllowed($plugin, $action);
}


/**
 *
 * Add new email to the queue. If possible, ImpressPages will send the email immediately.
 * If hourly email limit is exhausted, emails will be sent next hour.
 * ImpressPages always preserve 20% of hourly limit for urgent emails. So even if you have
 * just added thousands of non urgent emails, urgent emails will still be sent immediately.
 * Set $urgent parameter to false when delivery time is not so important, like newsletters, etc.
 * And set $urgent to true, when sending notification about purchase, etc.
 *
 *
 *
 * @param string $from Sender's e-mail address
 * @param string $fromName Sender's name
 * @param string $to Recipient's email address
 * @param string $toName  Recipient's name
 * @param string $subject Message subject
 * @param string $content Content to be sent (html or plain text. See $html attribute). If you need e-mail templates, use ipEmailTemplate() function to generate the content.
 * @param bool $urgent
 * @param bool $html
 * @param null $files
 */
function ipSendEmail($from, $fromName, $to, $toName, $subject, $content, $urgent=true, $html = true, $files = null)
{
    $emailQueue = new \Ip\Internal\Email\Module();
    $emailQueue->addEmail($from, $fromName, $to, $toName, $subject, $content, $urgent, $html, $files);
    $emailQueue->send();
}


/**
 *
 * Generate e-mail message HTML using given title, content, signature and footer
 * To send generated e-mail message HTML, use ipSendEmail() function.
 *
 * This function uses the default template, located at Internal/Config/view/email.php. Override the default template
 * @param array $data Associative array with template content. Default template outputs HTML using following array elements 'title', 'content', 'signature', 'footer'
 * @return string E-mail in HTML format
 */
function ipEmailTemplate($data)
{
    return ipView('Internal/Config/view/email.php', $data)->render();
}

/**
 * Get a view object from specified file and data
 * @param $file
 * @param array $data
 * @return \Ip\View
 */
function ipView($file, $data = array(), $_callerDepth = 0)
{
    if ($file[0] == '/' || $file[1] == ':') { // Absolute filename
        return new \Ip\View($file, $data);
    }

    if (preg_match('%^(Plugin|Theme|file|Ip)/%', $file)) {
        $relativePath = $file;
    } else {
        $relativePath = ipRelativeDir($_callerDepth + 1) . $file;
    }

    if (strpos($relativePath, 'Plugin/') === 0) {
        $overridePath = substr($relativePath, 7);
    } elseif (strpos($relativePath, 'Ip/Internal/')) {
        $overridePath = substr($relativePath, 12);
    } else {
        $overridePath = $relativePath;
    }

    $fileInThemeDir = ipThemeFile(\Ip\View::OVERRIDE_DIR . '/' . $overridePath);

    if (is_file($fileInThemeDir)) {
        return new \Ip\View($fileInThemeDir, $data);
    }

    $absolutePath = ipFile($relativePath);
    if (file_exists($absolutePath)) {
        // the most common case
        return new \Ip\View($absolutePath, $data);
    }

    // File was not found, check whether it is in theme override dir
    if (strpos($relativePath, 'Theme/' . ipConfig()->theme() . '/override/') !== 0) {
        throw new \Ip\Exception\View("View {$file} not found.");
    }

    $path = substr($relativePath, 'Theme/' . ipConfig()->theme() . '/override/');

    if (file_exists(ipFile('Ip/Internal/' . $path))) {
        $absolutePath = ipFile('Ip/Internal/' . $path);
    } elseif (file_exists(ipFile('Plugin/' . $path))) {
        $absolutePath = ipFile('Plugin/' . $path);
    } else {
        throw new \Ip\Exception\View("View {$file} not found.");
    }

    return new \Ip\View($absolutePath, $data);
}

/**
 * Get CMS storage object
 * CMS storage is a key-value storage, where any plugin can store it's data.
 * @return \Ip\Storage
 */
function ipStorage()
{
    return \Ip\ServiceLocator::storage();
}

// TODOX move to internal
function ipRelativeDir($callLevel = 0)
{
    if (PHP_VERSION_ID >= 50400) { // PHP 5.4 supports debug backtrace level
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $callLevel + 1);
    } else {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    }

    if (!isset($backtrace[$callLevel]['file'])) {
        throw new \Ip\Exception("Can't find caller");
    }

    $absoluteFile = $backtrace[$callLevel]['file'];

    if (DIRECTORY_SEPARATOR == '\\') {
        // Replace windows paths
        $absoluteFile = str_replace('\\', '/', $absoluteFile);
    }

    $overrides = ipConfig()->getRaw('FILE_OVERRIDES');
    if ($overrides) {
        foreach ($overrides as $relativePath => $fullPath) {
            if (DIRECTORY_SEPARATOR == '\\') {
                // Replace windows paths
                $fullPath = str_replace('\\', '/', $fullPath);
            }
            if (strpos($absoluteFile, $fullPath) === 0) {
                $relativeFile = substr_replace($absoluteFile, $relativePath, 0, strlen($fullPath));
                return substr($relativeFile, 0, strrpos($relativeFile, '/') + 1);
            }
        }
    }

    $baseDir = ipConfig()->getRaw('BASE_DIR');

    $baseDir = str_replace('\\', '/', $baseDir);
    if (strpos($absoluteFile, $baseDir) !== 0) {
        throw new \Ip\Exception('Cannot find relative path for file ' . $absoluteFile);
    }

    $relativeFile = substr($absoluteFile, strlen($baseDir) + 1);

    return substr($relativeFile, 0, strrpos($relativeFile, '/') + 1);
}

function ipPath($path)
{
    // Check if absolute path: '/' for unix, 'C:' for windows
    if ($path[0] == '/' || $path[1] == ':') {
        return $path;
    }

    // Check if relative path to root
    if (preg_match('%$(Plugin|Theme|file|Ip)/%', $path, $matches)) {

    }

    // Check if relative path to current path


}


/**
 * Get currently logged-in administrator id.
 * false if administrator is not logged-in
 * @return int | bool
 */
function ipAdminId()
{
    return \Ip\Internal\Admin\Service::adminId();
}
