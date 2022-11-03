<?php

// Auto Load all required classes

function loadClasses($class)
{

    $ext = ".php";
    $replace = str_replace('\\', '/', $class);

    $full_file_path = lcfirst($replace ). $ext;
    if (file_exists($full_file_path)) {
        include_once $full_file_path;
    } elseif (file_exists('../' . $full_file_path)) {
        include_once '../' . $full_file_path;
    } else {
        return false;
    }
}

spl_autoload_register('loadClasses');
session_start(); // Starting sessions immediately after loading classes;
