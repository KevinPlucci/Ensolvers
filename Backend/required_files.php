<?php
$reqfiles = array(
    DIR_config."config.php",
    DIR_includes.'common.inc.php'
);

if(count($reqfiles) > 0){
    foreach($reqfiles as $value){
        if(file_exists($value) && is_readable($value)){
            require_once($value);
        }
    }
}