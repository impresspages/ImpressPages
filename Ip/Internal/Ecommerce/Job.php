<?php

/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Ecommerce;


class Job
{

    /**
     * Subscription payment Url
     *
     * @param array $data
     * @return null
     */
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
            // Redirect to payment method.
            $paymentUrl = $paymentMethods[0]->paymentUrl($data);
            return $paymentUrl;
        }

        // Show payment selection window.
        return null;
    }

}
