<?php


/**
 * The Application Class
 * 
 * Handles the request for each call to the application.
 */
namespace app\core;


class App{

    private Request $request;

    private Response $response;

    public Router $router;

    public function __construct()
    {
        //initialize request and response objects

        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
    }

    public function run()
    {
        $this->router->resolve();
    }
 }
