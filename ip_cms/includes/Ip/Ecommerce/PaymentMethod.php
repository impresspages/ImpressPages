<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Ecommerce;


/**
 *
 * ImpressPages payment method interface
 *
 */
interface PaymentMethod
{
    /**
     * This method should generate payment URL.
     * Typical actions of this method:
     * 1 write down all passed data to database table
     * 2 return URL which start payment method execution
     *
     * @param string $module two separate plugins might have the same order ids'. So order ID without knowing plugin name, doesn't mean anything
     * @param int $orderId
     * @param string $itemTitle the one that will be displayed for user
     * @param int $price (in cents)
     * @param string $currency
     * @param array $options associative array of other options (eg. success return url, cancel url etc.)
     * @return string
     */
    public function generatePaymentUrl($module, $orderId, $itemTitle, $price, $currency, $options);
}
