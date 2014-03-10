<?php

$routes['day{/day}'] = 'day';

$routes['hello{/name}'] = function ($name = 'World') {
    $name = ucfirst($name);
    return "<h1>Hello $name!</h1>";
};

$routes['blog/archive/{month}']= array(
    'where' => array(
        'month' => '\d\d',
    ),
    'action' => 'blogArchive',
    'name' => 'blog.archive',
);

$routes['counter{/number}'] = array(
    'where' => array(
        'number' => '\d+',
    ),
    'name' => 'counter',
    'action' => function ($number = 1) {
        $content = '<h2>You have clicked counter ' . esc($number) . ' times</h2>';
        $content .= '<p><a href="' . ipRouteUrl('counter', array('number' => $number + 1)) . '">Click!</a></p>';
        return $content;
    },
);
