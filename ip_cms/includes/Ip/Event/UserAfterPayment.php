<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Ip\Event;


/**
 * ImpressPages CMS tries to separate payment methods from checkout or shopping cart logic
 * so that any shopping cart implementation or any other plugin might use any payment method
 * available within the system.
 *
 * Because of that payment method class doesn't know where to redirect user after successful / unsuccessful payment.
 * To deal with that, payment method throws this event to collect URLs available within the system
 * that could inform user about current payment status.
 *
 * Events has information about module that has created the order and order ID so that this event
 * could be caught by original payment initiator. But you might want to override default behaviour
 * by writing your own plugin and providing your custom URL. For that reason suggestUrl method of this
 * event has attribute 'priority'. To override default URL with your, just add your own URL with higher priority
 *
 */
class UserAfterPayment extends \Ip\Event{

    const USER_AFTER_PAYMENT = 'global.userAfterPayment';
    const STATUS_ERROR = 1;
    const STATUS_PENDING = 2;
    const STATUS_COMPLETED = 3;

    protected $module;
    protected $orderId;
    protected $paymentMethodClass;

    /**
     * where user should be redirected to
     * @var string
     */
    protected $url;

    /**
     * priority of current url. Many plugins might have their own opinion
     * which page to show for user. Wins the one that claim the highest priority.
     * @var int
     */
    protected $priority = -1;


    /**
     * All assigned url. Used mainly for debugging purposes
     * @var array
     */
    protected $allUrls;


    /**
     * @param object $object
     * @param string $module
     * @param int $orderId
     * @param string $paymentMethodClass
     * @param int $status
     */
    public function __construct($object, $module, $orderId, $paymentMethodClass, $status)
    {
        $this->module = $module;
        $this->orderId = $orderId;
        $this->paymentMethodClass = $paymentMethodClass;
        switch($status) {
            case self::STATUS_PENDING:
            case self::STATUS_ERROR:
            case self::STATUS_COMPLETED:
                $this->status = $status;
                break;
            default:
                throw new \Exception('Unknown status ('.$status.')');
        }


        parent::__construct($object, self::USER_AFTER_PAYMENT, array());
    }

    /**
     * Use this method when catching this event to suggest your URL
     * User will be redirected to URL with highest priority.
     * @param string $url
     * @param int $priority
     */
    public function suggestRedirectUrl($url, $priority)
    {
        if ($priority > $this->priority) {
            $this->priority = $priority;
            $this->url = $url;
        }
        $this->allUrls[] = array('url' => $url, 'priority' => $priority);
    }

    public function getRedirectUrl()
    {
        return $this->url;
    }

    public function getAllRedirectUrls()
    {
        return $this->allUrls;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getPaymentMethodClass()
    {
        return $this->paymentMethodClass;
    }

    public function getStatus()
    {
        return $this->status;
    }
}