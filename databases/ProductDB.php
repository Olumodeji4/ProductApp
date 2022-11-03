<?php

namespace databases;

use app\BaseQuery;

class ProductDB extends BaseQuery
{

    public function get()
    {
        var_dump('you got here');
        exit();
    }

    public function set_table()
    {
        $connection = DBConnection::get_connection();
        $connection->setTable();
        DBConnection::close_connection();
    }
}