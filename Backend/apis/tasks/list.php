<?php
/**
 * Task saaved in database
 */
    require_once(DIR_model."class.database.inc.php");
    $db = new cDB;
    if(!$db->Query("SELECT * FROM `task_list`")){
        return SendResponse(500,null,"No se pudo obtener la lista de tareas");
    }

    $list = array();
    if($fila = $db->First()){
        do{
            $list[] = $fila;
        }while($fila = $db->Next());
    }
    $db->EndConnection();

    if(count($list) == 0){
        return SendResponse(404,null,"Listado de tareas no encontrado");
    }
    SendResponse(200,$list);