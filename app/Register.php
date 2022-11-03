<?php

namespace app;

// Register ServiceProviders

use contracts\ProductInterface;
use repositories\ProductRepository;


class Register
{
// dynamically binding all interfaces to their concrete implementation
    protected $singleton = [
        ProductInterface::class => ProductRepository::class,
    ];

    // A Simple but chaotic way of choosing the right repo for the controllers
    public function getConcrete($class)
    {
        $singleton = $this->singleton;
        if ($singleton) {
            foreach ($singleton as $key => $value) {
                try {
                    if (new $class(new $value()) !== null) {
                        return new $class(new $value());
                    }
                } catch (\Exception $e) {
                     // echo $e->getMessage();
                }
            }
        }
    }

}












