<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Response;


class JsonRpc implements ResponseInterface
{
    protected $data;

    public function __construct($result = null, $errorMessage = null, $errorCode = null, $id = null)
    {
        $this->data = array();

        if ($errorCode === null && $errorMessage === null) {
            $this->data['result'] = $result;
        } else { // Error
            if ($result !== null) {
                throw new \Ip\CoreException('JSON 2.0 response should not contain result and error info at the same time');
            }
            $this->data['error']['code'] = $errorCode !== null ? $errorCode : 1;
            $this->data['error']['message'] = $errorMessage;
        }

        $this->data['id'] = $id;
    }

    public static function error($message, $code = 1, $id = null)
    {
        return new self(null, $message, $code, $id);
    }

    /**
     * JSON RPC 2.0 extension (adds response.error.name) for convenience reasons.
     *
     * @param string $name
     * @param string $message
     * @param int $code
     * @param int $id
     * @return JsonRpc
     */
    public static function errorName($name, $message = null, $code = 1, $id = null)
    {
        $jsonrpc = new self(null, $message, $code, $id);
        $jsonrpc->data['error']['name'] = $name;

        return $jsonrpc;
    }

    public static function result($data, $id = null)
    {
        return new self($data, null, null, $id);
    }

    public function isError()
    {
        return !empty($this->data['error']);
    }

    public function getErrorCode()
    {
        return isset($this->data['error']['code']) ? $this->data['error']['code'] : null;
    }

    public function getErrorMessage()
    {
        return isset($this->data['error']['message']) ? $this->data['error']['message'] : null;
    }

    public function getResult()
    {
        return array_key_exists('result', $this->data) ? $this->data['result'] : null;
    }

    public function send()
    {
        header('Content-type: text/json; charset=utf-8'); //throws save file dialog on firefox if iframe is used
        echo json_encode($this->utf8Encode($this->data));
    }

    /**
     *  Returns $dat encoded to UTF8
     * @param mixed $dat array or string
     */
    private function utf8Encode($dat)
    {
        if (is_string($dat)) {
            if (mb_check_encoding($dat, 'UTF-8')) {
                return $dat;
            } else {
                return utf8_encode($dat);
            }
        }
        if (is_array($dat)) {
            $answer = array();
            foreach($dat as $i=>$d) {
                $answer[$i] = $this->utf8Encode($d);
            }
            return $answer;
        }
        return $dat;
    }

}