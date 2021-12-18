<?php
//Constante de directorio base
if(isset($_SERVER['DOCUMENT_ROOT'])){
    define("DIR_BASE", $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR);
}else{
    define("DIR_BASE", dirname(__FILE__).DIRECTORY_SEPARATOR);
}