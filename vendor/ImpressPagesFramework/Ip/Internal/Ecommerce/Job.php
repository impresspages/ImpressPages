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
        $paymentMethods = Model::collectSubscriptionPaymentMethods($data);
        if (empty($paymentMethods)) {
            return '';
        }

        if (count($paymentMethods) == 1) {
            //redirect to payment method
            $paymentUrl = $paymentMethods[0]->paymentUrl($data);
            return $paymentUrl;
        }

        //show payment selection window
        //we will store all $data to the storage and assign a unique 32 character key
        //then we will redirect the user to the url with generated key.
        //this way we will have access to $data in payment selection window
        //$_SESSION is not good fit in this case because it will fail if user navigates checkout process several times in different tabs
        //and uses back / forward buttons of the browser.

        $key = Model::storePaymentData($data);
        $paymentSelectUrl = ipRouteUrl('Ecommerce_subscriptionPaymentSelect', array('key' => $key));
        return $paymentSelectUrl;
    }

    public static function ipPaymentUrl($data)
    {
        /**
         * @var \Ip\Payment[] $paymentMethods
         */
        $paymentMethods = Model::collectPaymentMethods($data);
        if (empty($paymentMethods)) {
            return '';
        }

        if (count($paymentMethods) == 1) {
            //redirect to the payment method as there is only one available
            $paymentUrl = $paymentMethods[0]->paymentUrl($data);
            return $paymentUrl;
        }

        //show payment selection window
        //we will store all $data to the storage and assign a unique 32 character key
        //then we will redirect the user to the url with generated key.
        //this way we will have access to $data in payment selection window
        //$_SESSION is not good fit in this case because it will fail if user navigates checkout process several times in different tabs
        //and uses back / forward buttons of the browser.

        $key = Model::storePaymentData($data);
        $paymentSelectUrl = ipRouteUrl('Ecommerce_paymentSelect', array('key' => $key));
        return $paymentSelectUrl;
    }

}
