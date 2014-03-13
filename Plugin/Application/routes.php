<?php

$routes['day{/day}'] = 'day'; // PublicController::day($day) will handle that

$routes['hello/{name}'] = function ($name) {
    $name = ucfirst($name);
    return '<h1>Hello ' . esc(ucfirst($name)) . '!</h1>';
};

$routes['counter{/number}'] = array(
    'where' => array(
        'number' => '\d+',
    ),
    'name' => 'counter', // route name to be used for ipRouteUrl() function
    'action' => function ($number = 1) {
        $content = '<h2>You have clicked counter ' . esc($number) . ' times</h2>';
        $content .= '<p><a href="' . ipRouteUrl('counter', array('number' => $number + 1)) . '">Click!</a></p>';
        return $content;
    },
);
