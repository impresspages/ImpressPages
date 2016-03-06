<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Response;


class JsonRpc extends Json
{
    public function __construct($result = null, $errorMessage = null, $errorCode = null, $id = null)
    {
        $data = array();

        if ($errorCode === null && $errorMessage === null) {
            $data['result'] = $result;
        } else { // Error
            if ($result !== null) {
                throw new \Ip\Exception('JSON 2.0 response should not contain result and error info at the same time');
            }
            $data['error']['code'] = $errorCode !== null ? $errorCode : 1;
            $data['error']['message'] = $errorMessage;
        }

        $data['id'] = $id;

        parent::__construct($data);
    }

    public static function error($message, $code = 1, $id = null)
    {
        return new self(null, $message, $code, $id);
    }

    public static function result($data, $id = null)
    {
        return new self($data, null, null, $id);
    }

    public function isError()
    {
        return !empty($this->content['error']);
    }

    public function getError()
    {
        return array_key_exists('error', $this->content) ? $this->content['error'] : null;
    }

    public function addErrorData($key, $value)
    {
        $this->content['error'][$key] = $value;

        return $this;
    }

    public function getResult()
    {
        return array_key_exists('result', $this->content) ? $this->content['result'] : null;
    }
}