<?php


use app\core\App;
use app\core\Exception\Handler;
use app\core\Session;

require_once '../vendor/autoload.php';


define('BASE_DIR', str_replace("\\", "/", dirname(__DIR__)));
define('IMAGES',   str_replace("\\", "/", __DIR__) . "/img/");
define('APP',  BASE_DIR . "/application/");


Handler::setHandler();

Session::init();


$app = new App();

define('PUBLIC_ROOT', $app->router->request->root());

$app->router->get('/', ['Login', 'index']);

$app->run();