<?php
/**
 * @package   ImpressPages
 */


namespace Ip;


class Ecommerce
{
    public function subscriptionPaymentUrl($options)
    {
        if (empty($options['item'])) {
            throw new \Ip\Exception('item name required');
        }
        $paymentUrl = ipJob('ipSubscriptionPaymentUrl', $options);
        return $paymentUrl;
    }
}
