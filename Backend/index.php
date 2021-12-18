<?php
header('Cache-Control: no-cache, must-revalidate"');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT, DELETE, GET, OPTIONS');
header('Access-Control-Request-Method: *');
header('Access-Control-Allow-Headers: *');
chdir(dirname(__FILE__));
require_once("initialize.php");
require_once('directories.php');//DIR constants
if(file_exists("required_files.php") AND is_readable("required_files.php")){
    require_once("required_files.php");//just in case
}

$uri = $_SERVER['REDIRECT_URL'] ?? null;
$query  = $_SERVER['QUERY_STRING'] ?? null;

$queryApi = explode("/",$uri);
array_shift($queryApi);
require_once(DIR_controllers."controller_api.php");
