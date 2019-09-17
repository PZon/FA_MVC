<?php

require dirname(__DIR__) . '/vendor/autoload.php';
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

session_start();

$router= new Core\Router();
$router->add('{controller}/{action}');
$router->add('',['controller'=>'Home', 'action'=>'index']);
$router->add('register',['controller'=>'Register', 'action'=>'register']);

$router->dispatch($_SERVER['QUERY_STRING']);
	