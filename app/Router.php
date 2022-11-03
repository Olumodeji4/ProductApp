<?php


namespace app;

use factories\ControllerFactory;

class Router
{
    protected ControllerFactory $factory;

    // get controller for current page request dynamically using the router class
    public function __construct()
    {
        $this->factory = new \factories\ControllerFactory();
    }

    /** Fetches the controller to use
     * @param Request $request
     * @return mixed
     */
    protected function get_controller(Request $request)
    {
        return $this->factory->use_router($request);
    }

    /**
     * Gets the method in the class from the controller
     * @param Request $request
     * @return mixed
     */
    public function get(Request $request)
    {
        $method = $request->get_method()??"products"; 
        return array($this->get_controller($request)->{$method}());
    }
}