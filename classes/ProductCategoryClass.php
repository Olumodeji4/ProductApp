<?php


namespace classes;


use app\BaseQuery;

class ProductCategoryClass extends BaseQuery
{

    public function __construct()
    {
        parent::__construct();
        $this->set_table(); // set table name, for this model class
        $this->relationship_id = 'product_category_id';
      // has many products
        $this->relationship_with = ['hasMany'=>'products'];
    }

    public function set_table()
    {
        $this->table = 'product_categories';
    }

}