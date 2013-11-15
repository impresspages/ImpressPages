<?php

namespace Ip;

class Response
{

    protected $statusCode = 200;
    protected $statusMessage = null;
    protected $headers = array();
    protected $content = null;

    public function __construct()
    {

    }

    public function addHeader($value)
    {
        $this->headers[] = $value;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param  int $code
     * @return Response
     */
    public function setStatusCode($code)
    {
        $this->statusCode = (int) $code;
        return $this;
    }

    /**
     * Retrieve HTTP status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param string $reasonPhrase
     * @return Response
     */
    public function setStatusMessage($message)
    {
        $this->statusMessage = $message;
        return $this;
    }

    /**
     * @return bool
     */
    public function isClientError()
    {
        $code = $this->getStatusCode();
        $error = $code < 500 && $code >= 400;
        return ($error);
    }

    /**
     * @return bool
     */
    public function isForbidden()
    {
        $forbidden = 403 == $this->getStatusCode();
        return $forbidden;
    }

    /**
     * @return bool
     */
    public function isInformational()
    {
        $code = $this->getStatusCode();
        $info = $code >= 100 && $code < 200;
        return $info;
    }

    /**
     * @return bool
     */
    public function isNotFound()
    {
        $notFound = 404 === $this->getStatusCode();
        return $notFound;
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        $ok = 200 === $this->getStatusCode();
        return $ok;
    }

    /**
     * @return bool
     */
    public function isServerError()
    {
        $code = $this->getStatusCode();
        $error =  500 <= $code && 600 > $code;
        return $error;
    }

    /**
     * @return bool
     */
    public function isRedirect()
    {
        $code = $this->getStatusCode();
        $redirect = 300 <= $code && 400 > $code;
        return $redirect;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        $code = $this->getStatusCode();
        $success = 200 <= $code && 300 > $code;
        return $success;
    }


    /**
     * @param String $content
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return String
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->getContent();
    }

    public function send()
    {
        /**
         * TODOX send event if status is 404

        $event = new \Ip\Event($this, 'site.beforeError404', null);
        \Ip\ServiceLocator::getDispatcher()->notify($event);
        if (!$event->getProcessed()) {
        \Ip\ServiceLocator::getDispatcher()->notify(new \Ip\Event($this, 'site.error404', null));
        }
        
         **/

        $headers = $this->getHeaders();
        foreach($headers as $header) {
            header($header);
        }
        if ($this->getStatusCode()) {
            if (function_exists('http_response_code')) {
                http_response_code($this->getStatusCode());
            } else {
                header('X-Ignore-This: workaround', true, $this->getStatusCode());
            }
        }
        echo $this->getContent();
    }


}