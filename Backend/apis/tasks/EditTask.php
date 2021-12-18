<?php
/**
 * Edit an existent task
 */
    require_once(DIR_model."class.tasks.inc.php");
    $tasks = new cTasks;
    $data = array();

    $id = FindParam("id,tarea_id,task_id");
    if(!$id OR !is_int(intval($id))){
        return SendResponse(400,null,"task_id is not present");
    }

    if(!$tasks->Get($id)){
        return SendResponse(404,null,"The with the provided ID has not found");
    }

    $name = FindParam("name,nombre,tarea,task");
    if(!empty($name)){
        $data['name'] = $name;
    }

    $status = FindParam("status,estado");
    if(!empty($status)){
        $data['status'] = $status;
    }

    $status = FindParam("status,estado");
    if(!empty($status)){
        $data['status'] = $status;
    }

    if(count($data) > 0){
        if(!$tasks->EditTask($data)){
            return SendResponse(500,null,"There is a problem editing the task");
        }
    }
    SendResponse(200,true,"The task has been edited successfully");