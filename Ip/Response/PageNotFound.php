<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Response;

/**
 *
 * Event dispatcher class
 *
 */
class PageNotFound extends \Ip\Response\Layout
{

    public function __construct($content = null, $headers = null, $statusCode = 404)
    {
        if ($content === null) {
            $content = $this->generateError404Content();
        }
        parent::__construct($content, $headers, $statusCode);
        $this->addHeader('HTTP/1.0 404 Not Found');
        $this->setTitle(__('Error 404', 'Ip', false));

    }

    public function getLayout()
    {
        if ($this->layout) {
            return $this->layout;
        }
        return is_file(ipThemeFile('404.php')) ? '404.php' : 'main.php';
    }

    protected function generateError404Content()
    {
        $data = array(
            'title' => __('Error 404', 'Ip', false),
            'text' => self::error404Message()
        );
        $content = ipView(ipFile('Ip/Internal/Config/view/error404.php'), $data)->render();
        return $content;
    }

    /**
     * Find the reason why the user come to non-existent URL
     * @return string error message
     */
    protected function error404Message()
    {
        $message = '';
        if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == '') {
            $message = __('Config.error_mistyped_url', 'Ip', false);
        } else {
            if (strpos($_SERVER['HTTP_REFERER'], ipConfig()->baseUrl()) < 5 && strpos(
                    $_SERVER['HTTP_REFERER'],
                    ipConfig()->baseUrl()
                ) !== false
            ) {
                $message = '<p>' . __('Config.error_broken_link_inside', 'Ip') . '</p>';
            } elseif (strpos($_SERVER['HTTP_REFERER'], ipConfig()->baseUrl()) === false) {
                $message = '<p>' . __('Config.error_broken_link_outside', 'Ip') . '</p>';
            }
        }
        return $message;
    }


}


