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

        return ipJob('ipGetProduct', $data);
    }



}

