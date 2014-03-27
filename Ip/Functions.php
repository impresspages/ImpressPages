<?php

/**
 * ImpressPages sugar methods
 */


/**
 * Get application object.
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
 * Get option value
 *
 * Options can be viewed or changed using administration pages. You can use this function to get your plugin settings.
 * @param string $option Option name. Option names use syntax "PluginName.optionName".
 * @param mixed|null $defaultValue Default value. Returned if the option was not set.
 * @return mixed Option value.
 */
function ipGetOption($option, $defaultValue = null)
{
    return \Ip\ServiceLocator::options()->getOption($option, $defaultValue);
}

/**
 * Get language specific option value
 *
 * @param string $option Option name. Option names use syntax "PluginName.optionName".
 * @param int $languageId Language ID.
 * @param mixed|null $defaultValue Default value. Returned if the option was not set.
 * @return mixed Option value.
*/

function ipGetOptionLang($option, $languageId, $defaultValue = null)
{
    return \Ip\ServiceLocator::options()->getOptionLang($option, $languageId, $defaultValue);
}

/**
 * Set option value
 *
 * You can use this function to set your plugin settings. Also, options can be viewed or changed using administration pages.
 *
 * @param string $option Option name. Option names use syntax "PluginName.optionName".
 * @param mixed $value Option value.
 */
function ipSetOption($option, $value)
{
    \Ip\ServiceLocator::options()->setOption($option, $value);
}

/**
 *  Set language specific option value
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
 * Remove option
 *
 * Options can be viewed or changed using administration pages.
 * @param $option Option name. Option names use syntax PluginName.optionName.
 */
function ipRemoveOption($option)
{
    return \Ip\ServiceLocator::options()->removeOption($option);
}

/**
 * Remove language specific option value
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
 * Get content object
 *
 * Use this object to access pages and languages.
 * @return \Ip\Content Content object.
 */
function ipContent()
{
    return \Ip\ServiceLocator::content();
}

/**
 * Add JavaScript file to a web page
 *
 * After adding all JavaScript files, issue ipJs() function to generate JavaScript links HTML code.
 *
 * @param string $file JavaScript file pathname. Can be provided as URL address, a pathname relative to current directory or to website root.
 * Place CSS files in assets subdirectory of a theme or a plugin.
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
            $relativePath = \Ip\Internal\PathHelper::ipRelativeDir(1) . $file;
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
 * Add JavaScript script. $value will be put inside <script> tags inline into HTML
 *
 * Generates JavaScript code which sets variables using specified values.
 *
 * @param string $name JavaScript variable name.
 * @param mixed $value Variable value. Note: Do not use object as a value.
 * @param int $priority JavaScript file priority. The lower the number the higher the priority.
 */
function ipAddJsContent($name, $value, $priority = 50)
{
    \Ip\ServiceLocator::pageAssets()->addJavascriptContent($name, $value, $priority);
}

/**
 * Add CSS file from your plugin or theme
 *
 * After adding all CSS files, use ipHead() function to generate HTML head.
 * @param string $file CSS file pathname. Can be provided as URL address, a pathname relative to current directory or to website root.
 * Place CSS files in assets subdirectory of a theme or a plugin.
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
            $relativePath = \Ip\Internal\PathHelper::ipRelativeDir(1) . $file;
        }

        $absoluteUrl = ipFileUrl($relativePath);
    }

    \Ip\ServiceLocator::pageAssets()->addCss($absoluteUrl, $attributes, $priority);
}

/**
 * Return log object
 *
 * Use this object to create or access log records.
 * @return \Psr\Log\LoggerInterface Logger interface object (\Ip\Internal\Log\Logger)
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
 * @param string $file Layout file name, e.g. "main.php".
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
 * Get response object
 *
 * @return \Ip\Response\Layout | \Ip\Response\Layout response object
 */
function ipResponse()
{
    return \Ip\ServiceLocator::response();
}

/**
 * Get current HTML layout name
 *
 * @return string HTML layout, e.g., "main.php".
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
 * Get block object
 *
 * @param string $block Block name, e.g. "main".
 * @return \Ip\Block Block object.
 */
function ipBlock($block)
{
    return \Ip\ServiceLocator::content()->generateBlock($block);
}


/**
 * Get slot object
 *
 * See slot documentation pages for more details.
 *
 * @param string $slot Slot name.
 * @param array|null $params Slot parameters.
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
 * @param string $event Event name, e.g. "MyPlugin_myEvent".
 * @param array $data Array with event data.
 * @return \Ip\Dispatcher Event dispatcher object.
 */
function ipEvent($event, $data = array())
{
    return \Ip\ServiceLocator::dispatcher()->event($event, $data);
}

/**
 * Filter data
 *
 * Fires an event for transforming a value.
 *
 * @param string $event Filter name, e.g. "MyPlugin_myFilter".
 * @param mixed $value Value to filter.
 * @param array $data Context array.
 * @return mixed Filtered value.
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
 * @return mixed|null Job result value.
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
 * @param string $string Unescaped string.
 * @return string Escaped string.
 */
function esc($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}


/**
 * Get escaped HTML text area content
 *
 * @param $value Unescaped string, containing HTML <textarea> tag content.
 * @return string Escaped string.
 */
function escTextarea($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Get escaped HTML attribute.
 * QUOTES ARE MANDATORY!!!
 * Correct example <div css="<?php echo escAttr() ?>">
 * Incorrect example <div css="<?php echo escAttr() ?>">
 * @param $value Unescaped HTML attribute.
 * @return string Escaped string.
 */

function escAttr($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Translate and escape a string
 *
 * @param $text Original value in English.
 * @param string $domain Context, e.g. plugin name.
 * @param string $esc Escape type. Available values: false, 'html', 'attr', 'textarea'.
 * @return string Translated string or original string if no translation exists.
 * @throws Ip\Exception
 */
function __($text, $domain, $esc = 'html')
{
    $translation = \Ip\ServiceLocator::translator()->translate($text, $domain);

    if ('html' == $esc) {
        return esc($translation);
    } elseif (false === $esc) {
        return $translation;
    } elseif ('attr' == $esc) {
        return escAttr($translation);
    } elseif ('textarea' == $esc) {
        return escTextarea($translation);
    }

    throw new \Ip\Exception('Unknown escape method: {$esc}');
}

/**
 * Translate, escape and then output a string
 *
 * @param $text Original value in English.
 * @param $domain Context, e.g. plugin name.
 * @param string $esc Escape type. Available values: false, 'html', 'attr', 'textarea'.
 */
function _e($text, $domain, $esc = 'html')
{
    echo __($text, $domain, $esc);
}

if (!function_exists('ipFile')) {
    /**
     * Gets absolute file path
     * @param $path A path or a pathname.
     * @return mixed|string Absolute path or pathname.
     */
    function ipFile($path)
    {
        global $ipFile_baseDir, $ipFile_overrides; // Optimization: caching these values speeds things up a lot

        if (!$ipFile_baseDir) {
            $ipFile_baseDir = ipConfig()->get('baseDir');
            $ipFile_overrides = ipConfig()->get('fileOverrides');
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
     * @param $path Pathname relative to current directory or root.
     * @return mixed|string File's URL address.
     */
    function ipFileUrl($path)
    {
        $overrides = ipConfig()->get('urlOverrides');
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
 *
 * @param array $query Associative (or indexed) array.
 * @return string URL string.
 */
function ipActionUrl($query)
{
    return ipConfig()->baseUrl() . '?' . http_build_query($query);
}

/**
 * @param string $route
 * @param array $params
 * @return string
 */
function ipRouteUrl($route, $params = array())
{
    return ipHomeUrl() . \Ip\ServiceLocator::router()->generate($route, $params);
}

/**
 * Get URL address of current theme folder
 *
 * @param $path Path or pathname relative to current theme directory.
 * @return mixed|string Theme's URL path
 */
function ipThemeUrl($path)
{
    return ipFileUrl('Theme/' . ipConfig()->theme() . '/' . $path);
}

/**
 * Gets the file path of the current theme folder
 *
 * @param $path  A path or a pathname relative to Theme/ directory.
 * @return mixed|string Absolute path or pathname.
 */

function ipThemeFile($path)
{
    return ipFile('Theme/' . ipConfig()->theme() . '/' . $path);
}

/**
 * Get homepage URL
 *
 * @return string Homepage URL address.
 */
function ipHomeUrl()
{
    $homeUrl = ipConfig()->baseUrl();
    if (ipConfig()->get('rewritesDisabled')) {
        $homeUrl .= 'index.php/';
    }

    if (ipGetOption('Config.multilingual')) {
        $homeUrl .= ipContent()->getCurrentLanguage()->getUrlPath();
    }

    return $homeUrl;
}

/**
 * Generate widget HTML
 *
 * @param $widgetName Widget name.
 * @param array $data Widget's data.
 * @param null $skin Widget skin name.
 * @return string Widget HTML.
 */
function ipRenderWidget($widgetName, $data = array(), $skin = null)
{
    $answer = \Ip\Internal\Content\Model::generateWidgetPreviewFromStaticData($widgetName, $data, $skin);
    return $answer;
}

/**
 * Get formatted currency string
 *
 * @param $price Numeric price. Multiplied by 100.
 * @param $currency Three letter currency code. E.g. "EUR".
 * @param string $context A context string: "Ip", "Ip-admin" or plugin's name.
 * @param null $languageId Language ID.
 * @return string A currency string in specific country format.
 */

function ipFormatPrice($price, $currency, $context, $languageId = null)
{
    return \Ip\Internal\FormatHelper::formatPrice($price, $currency, $context, $languageId);
}

/**
 * Get formatted date string
 *
 * @param $unixTimestamp Unix timestamp.
 * @param string $context A context string: "Ip", "Ip-admin" or plugin's name.
 * @param null $languageId Language ID.
 * @return string|null A date string formatted according to country format.
 */
function ipFormatDate($unixTimestamp, $context, $languageId = null)
{
    return \Ip\Internal\FormatHelper::formatDate($unixTimestamp, $context, $languageId);
}

/**
 * Get formatted time string
 *
 * @param $unixTimestamp Unix timestamp.
 * @param string $context A context string: "Ip", "Ip-admin" or plugin's name.
 * @param null $languageId Language ID.
 * @return string|null A time string formatted according to country format.
 */
function ipFormatTime($unixTimestamp, $context, $languageId = null)
{
    return \Ip\Internal\FormatHelper::formatTime($unixTimestamp, $context, $languageId);
}

/**
 * Get formatted date-time string
 *
 * @param $unixTimestamp Unix timestamp.
 * @param $context A context string: "Ip", "Ip-admin" or plugin's name.
 * @param null $languageId Language ID.
 * @return bool|mixed|null|string A date-time string formatted according to country format.
 */
function ipFormatDateTime($unixTimestamp, $context, $languageId = null)
{
    return \Ip\Internal\FormatHelper::formatDateTime($unixTimestamp, $context, $languageId);
}

/**
 * Get a theme option value.
 *
 * Theme options ar used for changing theme design. These options can be managed using administration page.
 * @param $name Option name.
 * @param mixed|null $default A value returned if the option was not set.
 * @return string Theme option value.
 */

function ipGetThemeOption($name, $default = null)
{
    $themeService = \Ip\Internal\Design\Service::instance();
    return $themeService->getThemeOption($name, $default);
}

/**
 * Get HTML attributes for <html> tag.
 *
 * @param int|null $doctype Doctype value. For constant value list, see \Ip\Response\Layout class definition.
 * @return string A string with generated attributes for <html> tag.
 */
function ipHtmlAttributes($doctype = null)
{
    $content = \Ip\ServiceLocator::content();
    if ($doctype === null) {
        $doctypeConstant = ipConfig()->get('defaultDoctype');
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
 *
 * @param int|null $doctype Doctype value. For constant value list, see \Ip\Response\Layout class definition.
 * @return string Document type declaration string.
 * @throws Exception
 */

function ipDoctypeDeclaration($doctype = null)
{
    if ($doctype === null) {
        $doctypeConstant = ipConfig()->get('defaultDoctype');
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
 * Get SQL table name by adding database prefix
 *
 * @param $table SQL table name without prefix.
 * @param string|null $as SQL "as" keyword to be added.
 * @return string Actual SQL table name.
 */
function ipTable($table, $as = false)
{
    $prefix = ipConfig()->tablePrefix();
    $answer = '`' . $prefix . $table . '`';
    if ($as === true) {
        if ($prefix) {  // if table prefix is empty we don't need to use `tableName` as `tableName`
            $answer .= ' as `' . $table . '` ';
        }
    } elseif ($as) {
        $answer .= ' as `' . $as . '` ';
    }

    return $answer;
}

/**
 * Check the permission to access plugin's administration
 *
 * Check if user has a right to access plugin's Admin controller.
 *
 * @param $plugin Plugin name.
 * @return bool Returns true if user has plugin's administration permission.
 */
function ipAdminPermission($permission, $userId = NULL)
{
    return \Ip\ServiceLocator::adminPermissions()->hasPermission($permission);
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
 * @return string Generated e-mail message in HTML format.
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
        $relativePath = \Ip\Internal\PathHelper::ipRelativeDir($_callerDepth + 1) . $file;
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
        $file = esc($file);
        throw new \Ip\Exception\View("View {$file} not found.");
    }

    $path = substr($relativePath, 'Theme/' . ipConfig()->theme() . '/override/');

    $possiblePath = ipFile($path);

    if (file_exists($possiblePath)) {
        $absolutePath = $possiblePath;
    } else {
        $file = esc($file);
        throw new \Ip\Exception\View("View {$file} not found.");
    }

    return new \Ip\View($absolutePath, $data);
}

/**
 * Get Key-Value storage object
 *
 * @return \Ip\Storage Storage object
 */
function ipStorage()
{
    return \Ip\ServiceLocator::storage();
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

/**
 * @param int|null $pageId
 * @return \Ip\PageStorage
 */
function ipPageStorage($pageId = NULL)
{
    if (!$pageId) {
        $page = ipContent()->getCurrentPage();
        if (!$pageId) {
            return null;
        }

        $pageId = $page->getId();
    }

    return new \Ip\PageStorage($pageId);
}

/**
 * @param string|null $theme
 * @return \Ip\ThemeStorage
 */
function ipThemeStorage($theme = NULL)
{
    if (!$theme) {
        $theme = ipConfig()->theme();
    }

    return new \Ip\ThemeStorage($theme);
}


/**
 * Get a modified copy of original file in repository
 * @param string $file (just filename. No path required)
 * @param \Ip\Transform $transform transformation object that does the modification of original file
 * @param string|null $desiredName desired filename of modified copy. A number will be added if desired name is already taken.
 * @return string path to modified copy starting from website's root. use ipFileUrl and ipFile functions to get full URL or full path to that file
 */
function ipReflection($file, \Ip\Transform $transform = null, $desiredName = null)
{
    $reflectionService = \Ip\Internal\Repository\ReflectionService::instance();
    return $reflectionService->getReflection($file, $transform, $desiredName);
}


/**
 * Get last exception of ipReflection method
 * @return \Ip\Internal\Repository\TransformException|null
 */
function ipReflectionException()
{
    $reflectionService = \Ip\Internal\Repository\ReflectionService::instance();
    return $reflectionService->getLastException();
}

/**
 * @param int $pageId
 * @return \Ip\Page
 */
function ipPage($pageId)
{
    return new \Ip\Page($pageId);
}

/**
 * Add file to the repository.
 * @param string $file absolute path to file in tmp directory
 * @param null|string $desiredName desired file name in repository.
 * @return string relative file name in repository
 * @throws \Ip\Exception
 */
function ipRepositoryAddFile($file, $desiredName = null)
{
    return \Ip\Internal\Repository\Model::addFile($file, $desiredName);
}


/**
 * Mark repository file as being used by plugin.
 * @param string $file
 * @param string $plugin
 * @param int $id single plugin might bind to the same file several times. In that case plugin might differentiate those binds by $id. If you sure this can't be the case for your plugin, use 1. You have to use the same id in ipUnbindFile
 */
function ipBindFile($file, $plugin, $id)
{
    \Ip\Internal\Repository\Model::bindFile($file, $plugin, $id);
}

/**
 * Release file binding
 * @param string $file
 * @param string $plugin
 * @param int $id single plugin might bind to the same file several times. In that case plugin might differentiate those bind by $id
 */
function ipUnbindFile($file, $plugin, $id)
{
    \Ip\Internal\Repository\Model::unbindFile($file, $plugin, $id);
}
