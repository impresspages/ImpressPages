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




























































//----------------------------------------------------------------------------------------------------------------------

//
//$router->add('Test.blog', 'blog/read/{id}{format}')
//    ->addTokens(array(
//            'id' => '\d+',
//            'format' => '(\.[^/]+)?',
//        ))->addValues(array(
//            'format' => '.html',
//        ));
//
//$router->add('Test.funky', 'funky/{name}');
//
//$route = $router->match($info['relativeUri']);
//
//if ($route) {
//    return $route->params;
//}
//
//$router->add(null, 'my/page')->addValues(array('action' => function() {
//
//}));
//
//$router->get('my/page', 'myPage', 'home');
//$router->get('my/page', 'Plugin\\Application\\AdminController::myPage', 'home');
//
//$router->map('GET','/users/[i:id]', 'users#show', 'users_show');
//$router->map('POST','/users/[i:id]/[delete|update:action]', 'usersController#doAction', 'users_do');
//
//// reversed routing
//$router->generate('users_show', array('id' => 5));
//
//
//ipRouter()->get('my/page', function() {
//    return 'Hello world!';
//});
//
//ipRouter()->get('books/{genre}', function ($genre) {
//    return 'Genre';
//});
//
//
//ipRouter()->get('books/{genre?}', function ($genre = 'Crime') {
//        return 'Genre';
//    });
//
//ipRouter()->get('month/{month}', array(
//   'as' => 'calendar',
//    function($month) {
//        return $month;
//    }
//));
//
//ipRouter()->get('month/{month}', array(
//    'as' => 'calendar',
//    'uses' => 'CalendarController@showCalendar'
//));
//
//
//ipRouter()->get('user/{name}', function($name)
//{
//    //
//})
//->where('name', '[A-Za-z]+');
//
//$name = Route::currentRouteName();
//
//Route::group(array('before' => 'auth'), function()
//{
//    Route::get('/', function()
//        {
//            // Has Auth Filter
//        });
//
//    Route::get('user/profile', function()
//        {
//            // Has Auth Filter
//        });
//});
//
