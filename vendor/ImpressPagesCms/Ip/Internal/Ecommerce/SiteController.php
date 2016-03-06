<?php
/**
 * @package   ImpressPages
 */


/**
 * Created by PhpStorm.
 * User: mangirdas
 * Date: 8/19/14
 * Time: 7:10 PM
 */

namespace Ip\Internal\Ecommerce;


class SiteController
{

    public function paymentSelection($key)
    {
        $data = Model::getPaymentData($key);
        $paymentMethods = Model::collectPaymentMethods($data);

        $paymentMethodName = ipRequest()->getPost('paymentMethod');
        if ($paymentMethodName) {
            //redirect to selected payment page
            foreach($paymentMethods as $paymentMethod) {
                if ($paymentMethod->name() == $paymentMethodName) {
                    $paymentUrl = $paymentMethod->paymentUrl($data['data']);
                    return new \Ip\Response\Json(array('redirect' => $paymentUrl));
                }
            }
        }

        //display all available payment methods
        ipAddJs('assets/paymentSelection.js');
        ipAddCss('assets/payments.css');
        $response = ipView('view/selectPayment.php', array('paymentMethods' => $paymentMethods));
        $response = ipFilter('ipPaymentSelectPageResponse', $response, array('paymentKey' => $key));
        return $response;

    }


    public function subscriptionPaymentSelection($key)
    {
        $data = Model::getPaymentData($key);
        $paymentMethods = Model::collectSubscriptionPaymentMethods($data);

        $paymentMethodName = ipRequest()->getPost('paymentMethod');
        if ($paymentMethodName) {
            //redirect to selected payment page
            foreach($paymentMethods as $paymentMethod) {
                if ($paymentMethod->name() == $paymentMethodName) {
                    $paymentUrl = $paymentMethod->paymentUrl($data['data']);
                    return new \Ip\Response\Json(array('redirect' => $paymentUrl));
                }
            }
        }

        //display all available payment methods
        ipAddJs('assets/paymentSelection.js');
        ipAddCss('assets/payments.css');
        $response = ipView('view/selectPayment.php', array('paymentMethods' => $paymentMethods));
        $response = ipFilter('ipSubscriptionPaymentSelectPageResponse', $response, array('paymentKey' => $key));
        return $response;

    }
}
