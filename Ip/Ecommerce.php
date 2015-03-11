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
            throw new \Ip\Exception('"item" setting is missing in subscriptionPaymentUrl function');
        }
        $paymentUrl = ipJob('ipSubscriptionPaymentUrl', $options);
        return $paymentUrl;
    }

    public function subscriptionCancelUrl($options)
    {
        if (empty($options['item'])) {
            throw new \Ip\Exception('"item" setting is missing in subscriptionCancelUrl function');
        }
        $cancelUrl = ipJob('ipSubscriptionCancelUrl', $options);
        return $cancelUrl;
    }

    public function paymentUrl($options)
    {
        if (empty($options['id'])) {
            throw new \Ip\Exception('"id" setting is missing in paymentUrl function');
        }
        if (empty($options['price'])){
            throw new \Ip\Exception('"price" setting is missing in paymentUrl function');
        }
        if (empty($options['currency'])) {
            throw new \Ip\Exception('"currency" setting is missing in paymentUrl function');
        }
        $paymentUrl = ipJob('ipPaymentUrl', $options);
        return $paymentUrl;
    }

}
