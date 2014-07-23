<?php
/**
 * @package   ImpressPages
 */


namespace Ip\Internal\Ecommerce;


class Job
{
    public static function ipSubscriptionPaymentUrl($data)
    {
        /**
         * @var \Ip\SubscriptionPayment[] $paymentMethods
         */
        $paymentMethods = ipFilter('ipSubscriptionPaymentMethods', array(), $data);
        if (empty($paymentMethods)) {
            return '';
        }

        if (count($paymentMethods) == 1) {
            //redirect to payment method
            $paymentUrl = $paymentMethods[0]->paymentUrl($data);
            return $paymentUrl;
        }

        //show payment selection window
        return null;
    }

    public static function ipPaymentUrl($data)
    {
        /**
         * @var \Ip\Payment[] $paymentMethods
         */
        $paymentMethods = ipFilter('ipPaymentMethods', array(), $data);
        if (empty($paymentMethods)) {
            return '';
        }

        if (count($paymentMethods) == 1) {
            //redirect to payment method
            $paymentUrl = $paymentMethods[0]->paymentUrl($data);
            return $paymentUrl;
        }

        //show payment selection window
        return null;
    }

}
