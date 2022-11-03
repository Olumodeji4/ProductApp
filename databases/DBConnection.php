<?php


namespace databases;


use contracts\ConnectionInterface;

final class DBConnection
{
    private static $db_instance = null; // database instance to use in ensuring one instance of database connection
    // all properties of this class is declared private so that it cannot be modified from the outside of this class
    private static $connection = null;

    // make sure no class can copy the database connection
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function get_connection(ConnectionInterface $connection = null)
    {
        self::init($connection);
        return self::$connection;
    }

    public static function close_connection()
    {
        self::$connection = null;
    }

    // This method returns any previous instance of this connection so that it remains constant
    private static function init(ConnectionInterface $connection = null)
    {
        // if instance of app is null, create new one
        if (self::$db_instance === null) {
            self::$db_instance = new self();
        }
        // if new connection is provided, use it else use the default one
        if ($connection) {
            self::$connection = $connection->get();
        } else if (self::$connection === null) {
            self::$connection = self::default_connection();
        }
        return self::$db_instance;
    }

    // new database connection can be added to this class but it can use mysql as default
    private static function default_connection()
    {
        return (new ConnectionDriver())->get();
    }

}