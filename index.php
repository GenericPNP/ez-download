<?php
//error_reporting(E_ALL); //uncomment for development server

define("BASE_PATH", str_replace("\\", NULL, dirname($_SERVER["SCRIPT_NAME"]) == "/" ? NULL : dirname($_SERVER["SCRIPT_NAME"])) . '/');

require_once "core/router.php";

Router::init();

function __autoload($class) { Router::autoload($class); }

Router::route();
?>