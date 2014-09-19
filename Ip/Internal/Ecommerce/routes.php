<?php

$routes['select-payment-method/{key}'] = array(
    'name' => 'Ecommerce_paymentSelect',
    'plugin' => 'Ecommerce',
    'controller' => 'SiteController',
    'action' => 'paymentSelection'
);

$routes['select-subscription-payment-method/{key}'] = array(
    'name' => 'Ecommerce_subscriptionPaymentSelect',
    'plugin' => 'Ecommerce',
    'controller' => 'SiteController',
    'action' => 'subscriptionPaymentSelection'
);
