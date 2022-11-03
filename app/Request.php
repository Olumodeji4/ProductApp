<?php


namespace app;


class Request
{
    private $current_route;

    // Get new url route or fetch the current url

    public function __construct($new_route = null)
    {
        $this->current_route = $new_route ?? $_SERVER['REQUEST_URI'];
    }

    /**
     * @return false|string[]
     */
    public function paraRoute()
    {
        $slash = '/';
        $route = $this->current_route;
        // Always getting the last two folders of our url
        $exploded = explode($slash, $route);
        return array_slice($exploded, -2);
    }

    /**
     * Get the Data Sent with the request
     * @param $name
     * @return mixed
     */
    public function get_query($name)
    {
        return $this->fetch_get_or_post($name);
    }

    /**
     * Check if a key exists in query get or post request
     * @param $name
     * @return bool
     */
    public function has_query($name): bool
    {
        return !empty($this->fetch_get_or_post($name));
    }


    public function all()
    {
        return $this->fetch_all_get_or_post();
    }

    private function fetch_all_get_or_post()
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Checks for both post and get requests
     * @param $key
     * @return mixed
     */
    private function fetch_get_or_post($key)
    {
        return $_POST[$key] ?? $_GET[$key];
    }


    /**
     * Gets the current page url as the method name for query
     * @return string
     */
    public function get_method(): string
    {
        return $this->paraRoute()[1];
    }
}