<?php


namespace classes;


use app\BaseQuery;

class ProductAttributeClass extends BaseQuery
{
    public function __construct()
    {
        parent::__construct();
        $this->set_table(); // set table name, for this model class
        $this->relationship_id = 'product_attribute_id';
        $this->relationship_with = ['belongsTo' => 'products'];

    }

    public function set_table()
    {
        $this->table = 'product_attributes';
    }
}