<?php
/***
* class: PdoMySql
* desc: wrapper for the PDO class
**/
class PdoMySql {
	
	private $db;
	
	public function __construct($host, $name, $username, $password) {
		try {
			$this->db = new PDO("mysql:host={$host};dbname={$name};charset=utf8", $username, $password);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			Debug('Construct db',$this->db);
		} catch(PDOException $e) {
			echo "Oops! There has been an error.";
			Debug('PDOException',$e);
			exit;
		}
	}
	
	public function pdo_query($sql, $params=false, $type, $error_msg) {
		try {
			$stmt = $this->db->prepare($sql);
			if ($params)
				foreach ($params as $name => $value)
					$stmt->bindValue($name, $value);
			$stmt->execute();
		} catch (PDOException $e) {
			if ($error_msg=='admin') echo "ERROR: ". $e->getMessage();
			else echo $error_msg;
			Debug('PDOException',$e);
			return false;
		}	
		switch($type) {
			case 'select_many':
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			break;
			case 'select_one':
				return $stmt->fetch(PDO::FETCH_ASSOC);
			break;
			case 'alter':
				if (isset($e)) return false;
				else return true;
			break;
		}
			
	}	
	
	public function last_insert_id() {
		return $this->db->lastInsertId();
	}
}
?>