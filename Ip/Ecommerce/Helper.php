<?php
/**
 * @package		Library
 */

namespace Ip\Ecommerce;


/**
 * Some function to speed up ecommerce products development
 * @package Library\Php\Ecommerce
 */
class Helper {


    protected function __construct(){}

    public static function instance()
    {
        return new Helper();
    }

    /**
     * Each product on ImpressPages is uniquely identified by module which stores that product and product id within that module
     * This method throws an event and finds product by module and id
     * @param string $module
     * @param string $productId
     * @param $options
     * @return \Ip\Ecommerce\Product
     */
    public function findProduct($module, $productId, $options)
    {
        $data = array(
            'module' => $module,
            'itemId' => $productId,
            'options' => $options
        );

        return ipDispatcher()->job('global.getProduct', $data);
    }

    //TODOX make sugar method
    /**
     * @param int $price in cents
     * @param string $currency three letter currency code
     * @param int $languageId
     * @return string
     */
    public function formatPrice($price, $currency, $languageId = null)
    {
        if ($languageId === null) {
            $languageId = ipContent()->getCurrentLanguage()->getId();
        }

        $data = array (
            'price' => $price,
            'currency' => $currency
        );
        $formattedPrice = ipDispatcher()->job('global.formatCurrency', $data);
        if ($formattedPrice === NULL) {
            if (function_exists('numfmt_create') && function_exists('numfmt_format_currency')) {
                $language = \Ip\ServiceLocator::content()->getLanguageById($languageId);
                $locale = str_replace('-', '_', $language->getCode());
                $fmt = numfmt_create( $locale, \NumberFormatter::CURRENCY );

                $formattedPrice = numfmt_format_currency($fmt, $price / 100, strtoupper($currency));
            } else {
                $formattedPrice = ($data['price']/100).' '.$data['currency'];
            }
        }
        return $formattedPrice;
    }

}

