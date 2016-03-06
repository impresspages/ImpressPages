<?php

namespace Ip;

/**
 * Controller response. Use to output from the controller.
 * @package Ip
 */
class Response
{

    protected $statusCode = null;
    protected $statusMessage = null;
    protected $headers = array();
    protected $content = null;

    public function __construct($content = null, $headers = null, $statusCode = 200)
    {
        if ($content !== null) {
            $this->setContent($content);
        }

        if ($statusCode !== null) {
            $this->setStatusCode($statusCode);
        }

        if ($headers !== null) {
            if (is_array($headers)) {
                $this->headers = $headers;
            } elseif (is_string($headers)) {
                $this->addHeader($headers);
            }
        }
    }

    /**
     * Add HTTP header
     *
     * @param $value
     */
    public function addHeader($value)
    {
        $this->headers[] = $value;
    }

    /**
     * Get HTTP headers
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set HTTP status code
     * @param  int $code Status code
     * @return Response
     */
    public function setStatusCode($code)
    {
        $this->statusCode = (int)$code;
        return $this;
    }

    /**
     * Retrieve HTTP status code
     *
     * @return int HTTP status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }


    /**
     * Set HTTP status message
     *
     * @param string $message Status message text
     * @return $this
     */
    public function setStatusMessage($message)
    {
        $this->statusMessage = $message;
        return $this;
    }

    /**
     * Check if HTTP status code is Client error
     * @return bool
     */
    public function isClientError()
    {
        $code = $this->getStatusCode();
        $error = $code < 500 && $code >= 400;
        return ($error);
    }

    /**
     * Check if HTTP status code is 403 Forbidden
     * @return bool
     */
    public function isForbidden()
    {
        $forbidden = 403 == $this->getStatusCode();
        return $forbidden;
    }

    /**
     * Check if a page returns HTTP informational status code
     * @return bool
     */
    public function isInformational()
    {
        $code = $this->getStatusCode();
        $info = $code >= 100 && $code < 200;
        return $info;
    }

    /**
     * Check if HTTP status is 404 Page not found
     * @return bool
     */
    public function isNotFound()
    {
        $notFound = 404 === $this->getStatusCode();
        return $notFound;
    }

    /**
     * Check if HTTP status is 200 Ok
     * @return bool
     */
    public function isOk()
    {
        $ok = 200 === $this->getStatusCode();
        return $ok;
    }

    /**
     * Check if HTTP status is Server error
     * @return bool
     */
    public function isServerError()
    {
        $code = $this->getStatusCode();
        $error = 500 <= $code && 600 > $code;
        return $error;
    }

    /**
     * Check if HTTP status is Redirect
     * @return bool
     */
    public function isRedirect()
    {
        $code = $this->getStatusCode();
        $redirect = 300 <= $code && 400 > $code;
        return $redirect;
    }

    /**
     * Check if HTTP status is Success
     * @return bool
     */
    public function isSuccess()
    {
        $code = $this->getStatusCode();
        $success = 200 <= $code && 300 > $code;
        return $success;
    }


    /**
     * Set webpage content
     * @param String $content
     * @return null
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get webpage content
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @ignore
     * @return string
     */
    public function toString()
    {
        return $this->getContent();
    }

    /**
     * Returns rendered version of a content
     *
     * @return string
     */
    public function render()
    {
        return $this->getContent();
    }

    /**
     * Sends a page to web browser
     */
    public function send()
    {
        $headers = $this->getHeaders();
        foreach ($headers as $header) {
            header($header);
        }
        if ($this->getStatusCode()) {
            if (function_exists('http_response_code')) {
                http_response_code($this->getStatusCode());
            } else {
                header('X-Ignore-This: workaround', true, $this->getStatusCode());
            }
        }
        echo $this->render();
    }

}
