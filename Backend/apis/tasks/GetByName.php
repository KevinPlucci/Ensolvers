<?php
/**
 * Get a task by his name
 */

    require_once(DIR_model."class.tasks.inc.php");
    $tasks = new cTasks;

    $name = FindParam("name,nombre,tarea,task");
    $name = (is_string($name))? trim($name):$name;
    if(empty($name) OR !is_string($name)){
        return SendResponse(400,null,"You need to provide the task name to search it");
    }
    
    if(!$data = $tasks->GetByName($name)){
        return SendResponse(404,null,"Task not found");
    }
    
    return SendResponse(200,$data,"Task found");