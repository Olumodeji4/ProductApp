<?php

namespace databases;

use contracts\ConnectionInterface;
use PDO;

/**
 *
 * Class ConnectionDriver
 * @package databases
 */
final class ConnectionDriver implements ConnectionInterface
{

    private static $servername = "localhost";
    private static $database = "id19789765_productappdb";
    private static $username = "id19789765_productapp";
    private static $password = "Mnbvlkj410..";

    /**
     * Get the database connection to be used
     * @param string $driver
     * @return PDO
     */
    public function get($driver = 'mysql')
    {
        switch ($driver) {
            case 'mysqlite':
                return $this->get_mysqlite();
            default:
                return $this->get_mysql();
        }
    }

    /**
     * Determine the different type of connection to be used
     * @return PDO
     */
    protected function get_mysql()
    {
        $conn = new PDO("mysql:host=" . self::$servername . ";dbname=" . self::$database, self::$username, self::$password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $conn;
    }

    /**
     * Other Connections can be indicated here
     */
    protected function get_mysqlite()
    {
        // Mysqlite connection goes here
        return;
    }
}