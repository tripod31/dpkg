<?php
    class SQLBuilder{
        public $base_sql;
        public $arr_cond;   #where条件の配列、andでつなげる
        public $order_by;
        
        function __construct() {
            $this->base_sql="";
            $this->arr_cond=array();   #where条件
            $this->order_by="";
        }
        
        function add_cond($cond){
            array_push($this->arr_cond, $cond);
        }
        
        function get_sql(){
            $sql = $this->base_sql;
            if (count($this->arr_cond)>0){
                $sql .= " WHERE ";
                for ($i=0;$i<count($this->arr_cond);$i++){
                    if ($i==0)
                        $sql .=   $this->arr_cond[$i];
                    else
                        $sql .=   " AND " . $this->arr_cond[$i];
                }
            }
            if (strlen($this->order_by)>0 )
                $sql .= " ORDER BY " . $this->order_by;
            
            return $sql;
        }
    }
?>