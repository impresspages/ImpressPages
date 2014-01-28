<?php

/**
 * ImpressPages sugar methods
 */


/**
 * Get CMS application object.
 * @return \Ip\Application Application object
 */
function ipApplication()
{
    return \Ip\ServiceLocator::application();
}

/**
 * Get security token string. Used to prevent XSRF attacks.
 *
 * Security token is a long random string generated for currently browsing user.
 * @return string Security token string.
 */
function ipSecurityToken()
{
    return ipApplication()->getSecurityToken();
}

/**
 * Get CMS option value
 *
 * Options can be viewed or changed using administration pages. You can use this function to get your plugin settings.
 * @param string $option Option name. Option names use syntax PluginName.optionName.
 * @param mixed|null $defaultValue Default value. Returned if option is not set.
 * @return mixed Option
 */
function ipGetOption($option, $defaultValue = null)
{
    return \Ip\ServiceLocator::options()->getOption($option, $defaultValue);
}

/**
 * Get language specific CMS option value
 *
 * @param string $option Option name. Option names use syntax PluginName.optionName.
 * @param int $languageId Language ID.
 * @param mixed|null $defaultValue Default value. Returned option is not set.
 * @return mixed Option value
*/

function ipGetOptionLang($option, $languageId, $defaultValue = null)
{
    return \Ip\ServiceLocator::options()->getOptionLang($option, $languageId, $defaultValue);
}

/**
 * Set CMS option value
 *
 * Options can be viewed or changed using administration pages. You can use this function to set your plugin settings.
 *
 * @param string $option Option name. Option names use syntax PluginName.optionName.
 * @param mixed $value Option value.
 */
function ipSetOption($option, $value)
{
    \Ip\ServiceLocator::options()->setOption($option, $value);
}

/**
 *  Set language specific CMS option value
 *
 * @param string $option Option name. Option names use syntax PluginName.optionName.
 * @param mixed $value Option value.
 * @param int $languageId Language ID.
 */
function ipSetOptionLang($option, $value, $languageId)
{
    \Ip\ServiceLocator::options()->setOptionLang($option, $languageId, $value);
}

/**
 * Remove CMS option
 *
 * Options can be viewed or changed using administration pages.
 * @param $option Option name. Option names use syntax PluginName.optionName.
 */
function ipRemoveOption($option)
{
    return \Ip\ServiceLocator::options()->removeOption($option);
}

/**
 * Remove language specific CMS option value
 *
 * @param string $option Option name. Option names use syntax PluginName.optionName.
 * @param int $languageId Language ID.
 */
function ipRemoveOptionLang($option, $languageId)
{
    return \Ip\ServiceLocator::options()->getOptionLang($option, $languageId);
}


/**
 * Get website configuration object
 *
 * Use website configuration object to access configuration values, such as base URL, debug mode, current theme, etc.
 *
 * @return \Ip\Config Configuration object.
 */
function ipConfig()
{
    return \Ip\ServiceLocator::config();
}



/**
 * Get CMS content object
 *
 * Use this object to access zones, pages and languages.
 * @return \Ip\Content Content object.
 */
function ipContent()
{
    return \Ip\ServiceLocator::content();
}

/**
 * Get current page object
 *
 * Use this object to get information about current page, language, zone.
 *
 * @return \Ip\CurrentPage Current page object
 */
function ipCurrentPage()
{
    return \Ip\ServiceLocator::currentPage();
}

/**
 * Add JavaScript file to a web page
 *
 * After adding all JavaScript files, issue ipJs() function to generate JavaScript links HTML code.
 *
 * @param string $file JavaScript file
 * @param array|null $attributes for example array('id' => 'example')
 * @param int $priority JavaScript file priority. The lower the number the higher the priority.
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
 * Add JavaScript variable
 *
 * Generates JavaScript code which sets variables using specified values.
 *
 * @param string $name JavaScript variable name.
 * @param mixed $value Variable value. Note: Do not use object as a value.
 */
function ipAddJsVariable($name, $value)
{
    \Ip\ServiceLocator::pageAssets()->addJavascriptVariable($name, $value);
}

/**
 * Add CSS file from your plugin or theme
 *
 * After adding all CSS files, use ipHead() function to generate HTML head.
 * @param string $file Full path to CSS file.
 * @param array $attributes Attributes for HTML <link> tag. For example, attribute argument array('id' => 'example') adds HTML attribute id="example"
 * @param int $priority CSS priority (loading order). The lower the number the higher the priority.
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
 * Return CMS log object
 *
 * Use this object to create or access log records.
 * @return \Psr\Log\LoggerInterface Logger interface object
 */
function ipLog()
{
    return \Ip\ServiceLocator::log();
}

/**
 * Generate HTML code for loading JavaScript files
 *
 * Generate HTML code which loads JavaScript files added by ipAddJs() function.
 * @return string HTML code with links to JavaScript files.
 */

function ipJs()
{
    return \Ip\ServiceLocator::pageAssets()->generateJavascript();
}

/**
 * Generate HTML head
 *
 * @return string Webpage HTML head
 */
function ipHead()
{
    return \Ip\ServiceLocator::pageAssets()->generateHead();
}

/**
 * Set HTML layout file
 *
 * @param string $file Layout file name
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
 * Get CMS response object
 *
 * @return \Ip\Response | \Ip\Response\Layout CMS response object
 */
function ipResponse()
{
    return \Ip\ServiceLocator::response();
}

/**
 * @ignore
 * @param \Ip\Page $page
 */
function _ipPageStart(\Ip\Page $page)
{
    ipCurrentPage()->_set('page', $page);

    ipEvent('_ipPageStart', array('page' => $page));
}

/**
 * Get current HTML layout
 *
 * @return string HTML layout, e.g., "main"
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
 * Get CMS block object
 *
 * @param string $block Block name, e.g. "main"
 * @return \Ip\Block Block object
 */
function ipBlock($block)
{
    return \Ip\ServiceLocator::content()->generateBlock($block);
}


/**
 * Get CMS slot object
 *
 * See slot documentation pages for more details.
 *
 * @param string $slot Slot name
 * @param array|null $params Slot parameters
 */
function ipSlot($slot, $params = array())
{
    return \Ip\ServiceLocator::slots()->generateSlot($slot, $params);
}

/**
 * Get management state
 *
 * Checks if the website is opened in management mode.
 * @return bool Returns true if the website is opened in management state.
 */

function ipIsManagementState()
{
    return \Ip\Internal\Content\Service::isManagementMode();
}

/**
 * Get HTTP request object
 *
 * HTTP request object can be used to get HTTP POST, GET and SERVER variables, and to perform other HTTP request related tasks.
 *
 * @return \Ip\Request Request object.
 */

function ipRequest()
{
    return \Ip\ServiceLocator::request();
}

/**
 * Trigger an event
 *
 * @param string $event Event name, e.g. "MyPlugin_myEvent"
 * @param array $data Array with event data
 * @return \Ip\Dispatcher Event dispatcher object.
 */
function ipEvent($event, $data = array())
{
    return \Ip\ServiceLocator::dispatcher()->event($event, $data);
}

/**
 * Filter data
 *
 * @param string $event Filter name, e.g. "MyPlugin_myFilter"
 * @param mixed $value Data to filter.
 * @param array $data Context array.
 * @return mixed Filtered data.
 */
function ipFilter($event, $value, $data = array())
{
    return \Ip\ServiceLocator::dispatcher()->filter($event, $value, $data);
}

/**
 * Create a job
 *
 * @param $eventName Job event name, e.g. "MyPlugin_myJob"
 * @param array $data Data for job processing.
 * @return mixed|null Job result.
 */
function ipJob($eventName, $data = array())
{
    return \Ip\ServiceLocator::dispatcher()->job($eventName, $data);
}


/**
 * Get database object
 *
 * Returns an object, which provides plugin developers with methods for connecting to database, executing SQL queries and fetching results.
 * @return \Ip\Db Database object.
 */
function ipDb()
{
    return \Ip\ServiceLocator::db();
}

/**
 * Get escaped text string
 *
 * @param string $string Unescaped string
 * @param string $esc html|attr|textarea|url|urlRaw|raw or false
 * @return string Escaped string
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
 * Get escaped text area content
 *
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
 * Translate and escape string
 *
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
 * Get URL address of current theme folder
 *
 * @param $path
 * @return mixed|string
 */
function ipThemeUrl($path)
{
    return ipFileUrl('Theme/' . ipConfig()->theme() . '/' . $path);
}

/**
 * Gets the file path of the current theme folder
 *
 * @param $path
 * @return mixed|string
 */

function ipThemeFile($path)
{
    return ipFile('Theme/' . ipConfig()->theme() . '/' . $path);
}

/**
 * Get homepage URL
 *
 * @return string Homepage URL address
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
 * Generate widget HTML
 *
 * @param $widgetName
 * @param array $data
 * @param null $skin Widget skin name.
 * @return string Widget HTML.
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
 * Get HTML document type declaration string
 * @param int|null $doctype Doctype value. For constant value list, see \Ip\Response\Layout class definition.
 * @return string Document type declaration string.
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
 * Get SQL table name by adding CMS database prefix
 *
 * @param $table SQL table name without prefix.
 * @param string|null $as SQL "as" keyword to be added.
 * @return string Actual SQL table name
 */
function ipTable($table, $as = null)
{
    $answer = '`' . ipConfig()->tablePrefix() . $table . '`';
    if ($as != false) {
        if ($as !== null) {
            $answer .= ' as ' . $as;
        } else {
            $answer .= ' as ' . $table;
        }
    }
    return $answer;
}

/**
 * Check the permission to access plugin's administration
 *
 * Check if user has a right to access plugin's Admin controller.
 *
 * @param $plugin Plugin name
 * @return bool Returns true if user has plugin's administration permission.
 */
function ipAdminPermission($plugin, $action = NULL)
{
    return \Ip\ServiceLocator::permissions()->isAllowed($plugin, $action);
}



/**
 * Send e-mail message
 *
 * Adds new e-mail to the queue. If possible, ImpressPages will send the email immediately.
 * If hourly e-mail limit is exhausted, emails will be sent in the next hour.
 * ImpressPages always preserve 20% of hourly limit for urgent emails. So even if you have
 * just added thousands of non urgent e-mails, urgent e-mails will still be sent immediately.
 * Set $urgent parameter to false when delivery time is not so important, like newsletters, etc.
 * And set $urgent to true, when sending notification about purchase, etc.
 *
 * @param string $from Sender's e-mail address
 * @param string $fromName Sender's name
 * @param string $to Recipient's email address
 * @param string $toName  Recipient's name
 * @param string $subject Message subject
 * @param string $content Content to be sent (html or plain text. See $html attribute). If you need e-mail templates, use ipEmailTemplate() function to generate the content.
 * @param bool $urgent E-mail urgency
 * @param bool $html HTML mode. Set to false for plain text mode.
 * @param string|array|null $files Full pathname of the file to be attached or array of the pathnames.
 */
function ipSendEmail($from, $fromName, $to, $toName, $subject, $content, $urgent=true, $html = true, $files = null)
{
    $emailQueue = new \Ip\Internal\Email\Module();
    $emailQueue->addEmail($from, $fromName, $to, $toName, $subject, $content, $urgent, $html, $files);
    $emailQueue->send();
}

/**
 * Generate e-mail HTML using template
 *
 * Generates e-mail message HTML using given template data, such as title, content, signature, footer, etc.
 * To send a message generated using ipEmailTemplate() function, use ipSendEmail() function.
 *
 * This function uses the default template, located at Internal/Config/view/email.php file. You can use your own template by overriding the default one.
 * @param array $data Associative array with template content. Default template outputs HTML using following array elements: 'title', 'content', 'signature', 'footer'.
 * @return string Generated e-mail message in HTML format
 */
function ipEmailTemplate($data)
{
    return ipView('Internal/Config/view/email.php', $data)->render();
}

/**
 * Get MVC view object
 *
 * Get a view object using specified view file and data array.
 *
 * @param $file MVC view file pathname
 * @param array $data View's data
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

    $fileInThemeDir = ipThemeFile(\Ip\View::OVERRIDE_DIR . '/' . $relativePath);

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

    $possiblePath = ipFile($path);

    if (file_exists($possiblePath)) {
        $absolutePath = $possiblePath;
    } else {
        throw new \Ip\Exception\View("View {$file} not found.");
    }

    return new \Ip\View($absolutePath, $data);
}

/**
 * Get CMS storage object
 *
 * CMS storage is a key-value storage, where any plugin can store it's data.
 * @return \Ip\Storage Storage object
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

/**
 * @param $path
 * @return mixed
 */
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
 * Get currently logged-in administrator ID
 *
 * @return int | bool Administrator ID. Returns false if not logged-in as administrator.
 */
function ipAdminId()
{
    return \Ip\Internal\Admin\Service::adminId();
}
