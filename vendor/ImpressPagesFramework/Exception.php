<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip;


class Exception extends \Exception
{
    // Redefine the exception so message isn't optional
    /**
     * @var array|null
     */
    protected $data;

    public function __construct($message, $data = null, $code = 0, \Exception $previous = null)
    {
        $this->data = $data;

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    public function getData()
    {
        return $this->data;
    }
}
