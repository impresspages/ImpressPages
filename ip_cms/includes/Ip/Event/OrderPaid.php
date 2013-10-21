<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Event;



class OrderPaid extends \Ip\Event{
    
    const ORDER_PAID = 'global.orderPaid';


    protected $module;
    protected $orderId;

    /**
     * @param object $object
     * @param string $module
     * @param int $orderId
     */
    public function __construct($object, $module, $orderId) {
        $this->module = $module;
        $this->orderId = $orderId;
        parent::__construct($object, self::ORDER_PAID, array());
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }
}