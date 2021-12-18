<?php
/**
 * Class for DB connection
 */
    if(!defined("DBHOST")){ require_once(DIR_config."config.php"); }
    $connected = false;
    class cDB{
        private $connection = null;
        private $host = DBHOST;
        private $user = DBUSER;
        private $pass = DBPASS;
        private $port = DBPORT;
        private $database = DBDATABASE;
        private $resultQuery = null;//Last result of executed query
        public $last_id = null;//Last inserted ID
        public $affected_rows = null;//Amount of rows affecteds on the last query (Update clause only?)
        public $ParseData = false;//Let fetched data of an SQL Query as object properties

        function __construct($host = null,$user = null,$pass = null,$port = null, $database = null)
        {
            if(!empty($host)){
                $this->host = $host;
            }
            if(!empty($user)){
                $this->user = $user;
            }
            if(!empty($pass)){
                $this->pass = $pass;
            }
            if(!empty($port)){
                $this->port = $port;
            }
            if(!empty($database)){
                $this->database = $database;
            }
            try {
                if(!$this->connection = mysqli_connect($this->host,$this->user,$this->pass,$this->database,$this->port)){ throw new Exception("Cannot connect to database"); }
            } catch (Exception $e) {
                $this->SetError($e);
            }
        }

        /**
         * Summary. Executes a query on the connected database
         * @param string $sql The query string
         * @return null|object
         */
        public function Query(string $sql):bool{
            try {
                if(!$this->connection){ throw new Exception("Connection with database are not established"); }
                if($this->resultQuery = $this->connection->query($sql)){ return true; }
            } catch (Exception $e) {
                $this->SetError($e);
            }
            return false;
        }

        /**
         * Summary. Obtain the first result of executed query
         * @param (optional) $mysqlResult The result of Query execution
         * @return null|object
         */
        public function First($mysqlResult = null):?array{
            try {
                $ruleSet = $mysqlResult ?? $this->resultQuery;
                if(!$ruleSet){ throw new Exception("The result of executed query is not valid"); }
                $ruleSet->data_seek(0);
                if($r = $ruleSet->fetch_assoc()){
                    if($this->ParseData){
                        $this->DataAsProperties($r);
                    }
                    return $r;
                }
            } catch (Exception $e) {
                $this->SetError($e);
            }
            return null;
        }

        /**
         * Summary. Obtain the next result of executed query
         * @param (optional) $mysqlResult The result of Query execution
         * @return null|object
         */
        public function Next($mysqlResult = null):?array{
            try {
                $ruleSet = $mysqlResult ?? $this->resultQuery;
                if(!$ruleSet){ throw new Exception("The result of executed query is not valid"); }
                if($r = $ruleSet->fetch_assoc()){
                    if($this->ParseData){
                        $this->DataAsProperties($r);
                    }
                    return $r;
                }
            } catch (Exception $e) {
                $this->SetError($e);
            }
            return null;
        }

        /**
         * Summary Inserts data to a table
         * @param string $table Table name
         * @param array $data The data to insert
         */
        public function Insert(string $table, array $data):bool{
            try {
                if(empty($table)){ throw new Exception("The table cannot be blank"); }
                if(count($data) == 0){ throw new Exception("The data cannot be empty"); }
                $data = $this->FilterData($table, $data);
                if(!$data){ throw new Exception("No hay datos para insertar o las columnas no coinciden con los datos dados"); }
                $data = $this->RealEscapeArray($data);
                $data['creation_date'] = Date("Y-m-d H:i:s");
                $data['modif_date'] = Date("Y-m-d H:i:s");
                $query = "INSERT INTO `".$table."`(%s) VALUES (%s)";
                $fields = "";
                $datas = "";
                foreach($data as $key => $value){
                    $fields = (empty($fields))? "`".$key."`":$fields.",`".$key."`";
                    if(is_array($value)){
                        $value = json_encode($value,JSON_PRETTY_PRINT|JSON_BIGINT_AS_STRING);
                    }
                    $datas = (empty($datas))? "'".$value."'":$datas.",'".$value."'";
                }
                $query = sprintf($query,$fields,$datas);
                if($this->connection->query($query)){
                    $this->last_id = $this->connection->insert_id;
                    return true;
                }
            } catch (Exception $e) {
                $this->SetError($e);
            }
            return false;
        }

        /**
         * Summary. Updates an existent TASK
         * @param string $table Table name
         * @param array $data The data to update
         * @param string $where The Where clause to use (cannot be empty)
         * @return bool
         */
        public function Update(string $table, array $data, string $where):bool{
            try {
                if(empty($table)){ throw new Exception("The table cannot be blank"); }
                if(empty($where)){ throw new Exception("The where clause cannot be empty"); }
                if(count($data) == 0){ throw new Exception("The data cannot be empty"); }
                $where = preg_replace("/^where/i","",$where);
                $data = $this->FilterData($table, $data);
                if(!$data){ throw new Exception("No hay datos para insertar o las columnas no coinciden con los datos dados"); }
                $data = $this->RealEscapeArray($data);
                $data['modif_date'] = Date("Y-m-d H:i:s");//Always update modif date to the current date
                $query = "UPDATE `".$table."` SET %s WHERE ".$where;
                $datas = "";
                foreach($data as $key => $value){
                    $str = "`".$key."`='".$value."'";
                    $datas = (empty($datas))? $str:$datas.",".$str;
                }
                $query = sprintf($query,$datas);
                if($this->connection->query($query)){
                    $this->affected_rows = $this->connection->affected_rows;
                    return ($this->affected_rows > 0)? true:false;
                }
            } catch (Exception $e) {
                $this->SetError($e);
            }
            return false;
        }

        public function EndConnection(){
            mysqli_close($this->connection);
        }
        public function SetError(Exception $e){
            $msg = $e->getMessage();
            echo $msg;
            $this->EndConnection();
        }

        // SHOW COLUMNS FROM `task_list`
        private function FilterData(string $table, $data){
            if(!is_array($data) OR count($data) == 0){ return null; }
            $query = "SHOW COLUMNS FROM `".$table."`";
            $rs = $this->Query($query);
            if(!$fila = $this->First()){ return null; }
            $fields = array();
            do{
                $fields[] = $fila['Field'];
            }while($fila = $this->Next());
            unset($fields['id']);
            if(!is_array($fields) OR count($fields) == 0){ return null; }
            $tmpData = array();
            foreach($data as $key => $value){
                if(in_array($key,$fields)){
                    $tmpData[$key] = $value;
                }
            }
            return (count($tmpData) > 0)? $tmpData:null;
        }

        /**
         * Real escape array
         */
        public function RealEscapeArray(array $data){
            foreach($data as $key => $value){
                if(is_string($value)){
                    $data[$key] = $this->RealEscape($value);
                    continue;
                }
                if(is_array($value)){
                    $data[$key] = $this->RealEscapeArray($value);
                }
            }
            return $data;
        }
        
        /**
         * Escapes an string to be inserted on DB
         */
        public function RealEscape(string $data){
            $result = $data;
            if(!$this->connection){ return $data; }
            $data = $this->connection->real_escape_string($data);
            return $data;
        }

        /**
         * Summary. Let the array as propeties of class
         */
        protected function DataAsProperties(array $data):void{
            if(count($data) == 0){ return; }
            foreach($data as $key => $value){
                $this->$key = $value;
            }
        }
    }