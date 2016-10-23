<?php
class BlogIpModel{
	public function __construct(){
		$arrConfig = Yaf_Application::app()->getConfig();//strtolower
		$this->pdo = new PDO("$arrConfig->DB_TYPE:host=$arrConfig->DB_HOST;dbname=$arrConfig->DB_NAME","$arrConfig->DB_USER","$arrConfig->DB_PWD");
		$this->table_name = "blog_ip";
	}
	
	public function select($parms,$type=0){
		
		$field = '*';
		if(isset($parms['field'])){
			$field = $parms['field'];
			//echo $quere = "select * from ".$this->table_name." ".$left.$where.$order.$limit;die;
		}
		
		$where = '';
		if(isset($parms['where'])){
			$where = ' where '.$parms['where'];
		}
		$order = '';
		if(isset($parms['order'])){
			$order = ' order by '.$parms['order'];
		}
	
		$limit='';
		if(isset($parms['limit'])){
			$limit = $parms['limit'];
		}
		$left = '';
		if(isset($parms['left'])){
			$left = $parms['left'];
			//echo $quere = "select * from ".$this->table_name." ".$left.$where.$order.$limit;die;
		}
		
		
		$quere = "select ".$field." from ".$this->table_name." ".$left.$where.$order.$limit;
		if($type){
			$quere = "select count(*) as id from ".$this->table_name." ".$left.$where.$order.$limit;
		}
		
		//echo $quere = "select * from blog_entry where id =1  order by abstract desc,created desc limit 0 offset 10";
		$rs = $this->pdo->query("$quere");
		//$rs = $this->pdo->query("select * from blog_entry");
		
		$row = $rs->fetchAll(PDO::FETCH_ASSOC);
		if($type){
			
			if(!empty($row[0]['id'])){
				return $row[0]['id'];
			}
			return 0;
		}	
		return $row;
	}

	public function insert($parms){
		$i=0;
		foreach($parms as $key=>$pa){
			if($i==0){
				$name = '`'.$key.'`';
				$value = "'".$pa."'";
			}else{
				$name .= ",`".$key.'`';
				$value .= ",'".$pa."'";
			}
			$i++;
		}
		if(!$name && !$value){
			return 0;
		}
		$result = $this->pdo->exec("insert into ".$this->table_name." (".$name.") values (".$value.")");
		if($result){
			return $this->pdo->lastinsertid();
		}else{
			return 0;
		}
	}
	
	public function update($parms,$where){
		$parm = "";
		foreach($parms as $key=>$par){
			if($parm == ""){
				$parm .= $key." = '".$par."'";
			}else{
				$parm .= ",".$key." = '".$par."'";
			}
		}
		$wher = "";
		foreach($where as $key=>$whe){
			if($wher == ""){
				$wher .= $key." = '".$whe."'";
			}else{
				$wher .= ",".$key." = '".$whe."'";
			}
		}
		
		$query = "update ".$this->table_name." set ".$parm." where ".$wher."";
		$result = $this->pdo->exec($query);
		return $result;
	}
	
	public function find($parms){
		$where = '';
		if(isset($parms['where'])){
			$where = ' where '.$parms['where'];
		}
		$order = '';
		if(isset($parms['order'])){
			$order = ' order by '.$parms['order'];
		}
	
		$limit=' limit 1';
		
		$quere = "select * from ".$this->table_name." ".$where.$order.$limit;
		
		$rs = $this->pdo->query("$quere");
		
		$row = $rs->fetchAll(PDO::FETCH_ASSOC);
			
		return $row;
	}
}

?>
