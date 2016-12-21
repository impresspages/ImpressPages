<?php
/**
 * @package   ImpressPages
 */


/**
 * Created by PhpStorm.
 * User: mangirdas
 * Date: 8/20/14
 * Time: 10:57 AM
 */

namespace Ip\Internal\Ecommerce;


class Model
{
    /**
     * @param $paymentData
     * @return \Ip\Payment[]
     */
    public static function collectPaymentMethods($paymentData)
    {
        return ipFilter('ipPaymentMethods', [], $paymentData);
    }

    /**
     * @param $paymentData
     * @return \Ip\SubscriptionPayment[]
     */
    public static function collectSubscriptionPaymentMethods($paymentData)
    {
        return ipFilter('ipSubscriptionPaymentMethods', [], $paymentData);
    }


    /**
     * @param array $paymentData
     * @return string unique 32 character key of stored data
     */
    public static function storePaymentData($paymentData)
    {
        $key = \Ip\Lib\Random::string(32);
        //loop till we get a unique key
        while(ipStorage()->get('Ecommerce', 'payment_' . $key)) {
            $key = \Ip\Lib\Random::string(32);
        }

        $info = array(
            'data' => $paymentData,
            'time' => time()
        );

        ipStorage()->set('Ecommerce', 'payment_' . $key, $info);
        return $key;
    }

    public static function getPaymentData($key)
    {
        return ipStorage()->get('Ecommerce', 'payment_' . $key);
    }

}
