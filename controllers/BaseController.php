<?php


namespace controllers;


abstract class BaseController
{
    /**
     * This method is used for generating all views for this app
     * @param $view_name
     * @param array $data
     * @return mixed
     */
    protected function get_view($view_name, $data = [])
    {
        if ($data) {
            // extracts all the data and pass along to the included file
            extract($data, EXTR_OVERWRITE);
        }
        return include('views/' . $view_name . '.php');
    }

    /**
     * Redirects to a new page
     * @param $url
     * @param bool $permanent
     */
    protected function redirect_to($url, $permanent = true)
    {
        echo "<script type='text/javascript'>document.location.href='{$url}';</script>";
        echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $url . '">';
        //header('Location: ' . $url, true, $permanent ? 301 : 302);
     //   exit();
    }
}