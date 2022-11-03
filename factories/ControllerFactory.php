<?php


namespace factories;


use App\Register;
use app\Request;
use controllers\ProductsController;

class ControllerFactory
{

    /**
     * Returning the right controller for the requested page
     * @param $route
     * @return mixed
     */
    public function make($route)
    {
        $register = new Register();
        $get_route = $route[1] ?? $route[0];
        // new controllers can come in here
        // e.g case "add-payment":

        switch ($get_route) {
            default:
                return $register->getConcrete(ProductsController::class);
        }
    }

    /**
     * We use the default router provided for fetching our routes
     * @param Request $request
     * @return mixed
     */
    public function use_router(Request $request)
    {
        return $this->make($request->paraRoute());
    }

}