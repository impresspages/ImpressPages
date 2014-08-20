<?php

$routes['select-payment-method/{key}'] = array(
    'name' => 'Ecommerce_paymentSelect',
    'plugin' => 'Ecommerce',
    'controller' => 'SiteController',
    'action' => 'paymentSelection'
);
