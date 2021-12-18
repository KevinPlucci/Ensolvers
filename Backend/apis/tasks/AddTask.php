<?php
/**
 * Create a new Task
 */
    require_once(DIR_model."class.tasks.inc.php");
    $tasks = new cTasks;
    $data = array();

    $name = FindParam("name,nombre,tarea,task");
    if(empty($name)){
        return SendResponse(400,null,"Debes indicar un nombre");
    }
    $data['name'] = $name;

    $status = FindParam("status,estado");
    if(!empty($status)){
        $data['status'] = $status;
    }

    if(!$tasks->CreateTask($data)){
        return SendResponse(500,null,"No se pudo crear la tarea");
    }
    
    SendResponse(201,$tasks->last_id,"Tarea creada con éxito");