<?php
/**
 * @package   ImpressPages
 */


namespace Ip;


abstract class SubscriptionPayment
{

    public abstract function name();

    public abstract function icon($width = null, $height = null);

    /**
     * This method should generate payment URL.
     * Typical actions of this method:
     * 1 write down all passed data to database table
     * 2 return URL which starts payment method execution
     *
     * @param array $data subscription data
     */
    public abstract function paymentUrl($data);
}
