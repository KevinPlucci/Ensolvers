<?php
    require_once(DIR_model."class.database.inc.php");
    class cTasks extends cDB{
        private $table = "task_list";

        function __construct()
        {
            parent::__construct();
            $this->ParseData = true;
        }

        public function Get(int $id):?array{
            try{
                if(!is_int($id)){ throw new Exception("The task ID to get its'n an integer"); }
                $query = "SELECT * FROM `".$this->table."` WHERE `id`=".$id;
                return ($this->Query($query))? $this->First():null;
            } catch (Exception $e) {
                $this->SetError($e);
            }
            return null;
        }

        public function GetByName(string $name){
            try{
                if(empty($name)){ throw new Exception("The task name is empty"); }
                $query = "SELECT * FROM `".$this->table."` WHERE LOWER(`name`)=LOWER('".$this->RealEscape($name)."')";
                return ($this->Query($query))? $this->First():null;
            } catch (Exception $e) {
                $this->SetError($e);
            }
            return null;   
        }

        /**
         * Summary. Create a new task
         * @param array $data The data to create with
         * @return bool
         */
        public function CreateTask(array $data):bool{
            try {
                if(!is_array($data) OR count($data) == 0){ throw new Exception("Data isn't an array"); }
                return $this->Insert($this->table,$data);
            } catch (Exception $e) {
                $this->SetError($e);
            }
            return false;
        }

        /**
         * Summary. Edit an existend task
         * @param array $data The data to be edited
         * @return bool
         */
        public function EditTask(array $data):bool{
            try {
                if(!is_array($data) OR count($data) == 0){ throw new Exception("Data isn't an array"); }
                return $this->Update($this->table,$data,"`id`=".$this->id);
            } catch (Exception $e) {
                $this->SetError($e);
            }
            return false;
        }
    }