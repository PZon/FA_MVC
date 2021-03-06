<?php

require dirname(__DIR__) . '/vendor/autoload.php';
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

session_start();

$router= new Core\Router();
$router->add('{controller}/{action}');
$router->add('',['controller'=>'Home', 'action'=>'index']);
$router->add('login',['controller'=>'Home', 'action'=>'index']);
$router->add('register',['controller'=>'Register', 'action'=>'register']);
$router->add('logout',['controller'=>'Login', 'action'=>'destroy']);
/************************************/
$router->add('passwords/reset/{token:[\da-f]+}',['controller'=>'Passwords', 'action'=>'reset']);
$router->add('register/activate/{token:[\da-f]+}',['controller'=>'Register', 'action'=>'activate']);

$router->dispatch($_SERVER['QUERY_STRING']);
	