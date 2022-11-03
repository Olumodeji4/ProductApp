<?php


namespace contracts;


interface ConnectionInterface
{

    /**
     * Gets the database connection
     * @return mixed
     */
    public function get();
}