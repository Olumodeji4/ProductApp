<?php

namespace controllers;

use app\Request;
use contracts\ProductInterface;

class ProductsController extends BaseController
{
    protected $repo;

    public function __construct(ProductInterface $product)
    {
        $this->repo = $product;
    }

    /**Get all products information page
     * @return mixed
     */
    public function products()
    {
        return $this->get_view('products', ['products' => $this->repo->get_product_list()]);
    }

    /**
     * Add new Product page
     * @return mixed
     */
    public function add_product()
    {
        return $this->get_view('add-product');
    }


    public function store_product()
    {
        $request = new Request();

        $product = $this->repo->store_products($request->all());

        if ($product) {
            $this->redirect_to('products');
        }

        $this->redirect_to('add-product');
    }

    public function create_demo_products()
    {
        $this->repo->add_demo_data();
        $this->redirect_to('products');
    }

    /**
     * @return false|string
     */
    public function mass_delete()
    {
        $request = new Request();
        $products_ids = $request->get_query('ids');
        if ($products_ids) {
            $products_ids = (array)$products_ids;
            $this->repo->mass_delete($products_ids);
        }
        return json_encode(['response' => true]);
    }


    /**
     * This method ensures that when the class extending this base class is called,
     * the method name can be fetched dynamically so add-product === add_product
     * @throws \Exception
     */
    public function __call($method, $parameters)
    {
        $method = str_replace("-", "_", $method);
        if (method_exists(__CLASS__, $method)) {
            return $this->{$method}($parameters);
        }
        $this->products(); //return default route if route not found
    }
}