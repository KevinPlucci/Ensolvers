<?php
/**
 * Main controller for apis
 */

$sendedResponse = false;
try {
    $method = $_SERVER['REQUEST_METHOD'];
    if(in_array($method,['OPTION','OPTIONS'])){
        header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
        die();
    }
    if(!isset($queryApi) OR !is_array($queryApi) OR count($queryApi) == 0){ throw new Exception("Petición incorrecta",400); }
    if(!file_exists(DIR_apis)){ throw new Exception("",500); }
    $targetApi = DIR_apis;
    
    $file = array_pop($queryApi);
    if(count($queryApi) > 0){
        foreach($queryApi as $value){
            if(!file_exists($targetApi.$value)){ throw new Exception("Targeted api not found",404); }
            $targetApi .= $value.DS;
        }
    }
    $targetApi .= $file.".php";
    if(!file_exists($targetApi) OR !is_readable($targetApi)){ throw new Exception("Targeted api not found",404); }
    require_once($targetApi);
    if(!$sendedResponse){ SendResponse(200,"success!"); }
} catch (Exception $e) {
    $code = $e->getCode();
    if($code < 100){ $code = 400; }
    $msg = $e->getMessage();
    if(empty($msg)){ $msg = "Object not found"; }
    SendResponse($code,null,$msg);
}

/**
 * Send an HTTP response to the client
 */
function SendResponse(int $http_code, $data, string $msg = null){
    global $sendedResponse;
    if($sendedResponse){ return; }

    header("Content-type: application/json; charset=UTF-8");
    if(!is_int($http_code)){
        $http_code = 404;
    }

    $response = $data;
    
    $response = array(
        'http_code' => $http_code,
        'data' => $response,
        'msg' => $msg,
        'time' => time()
    );
    
    header($_SERVER['SERVER_PROTOCOL']." ".$http_code);
    echo json_encode($response,JSON_PRETTY_PRINT|JSON_OBJECT_AS_ARRAY|JSON_BIGINT_AS_STRING|JSON_INVALID_UTF8_IGNORE);
    $sendedResponse = true;
}

/**
 * Get x param from the request
 * @param string|array $find List of params to find
 * @return mixed
 */
function FindParam($find){
    if(is_string($find)){
        $find = explode(",",$find);
    }

    if(!is_array($find)){
        $find = (array)$find;
    }

    $data = $_REQUEST ?? array();
    $bodyData = file_get_contents("php://input");
    if(!empty($bodyData)){
        if($json = json_decode($bodyData,true)){
            $data = array_merge($data,$json);
        }
    }

    if(count($data) == 0){ return null; }
    $find = array_map("strtolower",$find);

    foreach($data as $key => $value){
        if(in_array(strtolower($key),$find)){
            return $value;
        }
    }
    
    return null;
}