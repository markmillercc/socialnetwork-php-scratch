<?php
/***
* class: Registrar
* desc: Handles user registration and login
**/
class Registrar {
	
	private $db;
	
	/*
	* CONSTRUCTOR($db)
	*	$db is an instance of class PdoMySql
	***/
	public function __construct($db) {
		$this->db = $db;
	}
	
	/*
	* FUNCTION registerNew($user_info)
	*	- $user_info is an assoc. array in the form of:
	*		'column name'=>'value to insert'
	*	- array must include values for indices: 
	*		'email', 'first_name', 'last_name'
	*	- if $fb_id == false, 'password' is also required
	*	- if $fb_id != false, user logged in with Facebook
	***/
	public function registerNew($user_info, $fb=false) {
		
		Debug('New User Reg info', $user_info);
		
		// Check for required fields
		$missing = array();
		if (!isset($user_info['email']))
			$missing[] = 'email'; 
		if (!isset($user_info['first_name']))
			$missing[] = 'first name';
		if (!isset($user_info['last_name']))
			$missing[] = 'last name';
		
		if (!$fb) {
            if (!isset($user_info['password'])) 
				$missing[] = 'password';
			else 
				$user_info['password'] = password_hash($user_info['password'], PASSWORD_DEFAULT);   
        }			
		
		if ($missing) {
			echo "Missing required field(s): ";
			$comma = '';
			foreach($missing as $m) {
				echo $comma.$m;
				$comma = ', ';
			}
			return false;
		}
		
		// Check if email exists already
		$sql = "SELECT id FROM users WHERE email = '{$user_info['email']}';";
		if ($this->db->pdo_query($sql, false, 'select_one', 'Error checking for previous account')) {
			Debug('Check for existing email', $sql);
			echo "That email already exists";
			return false;
		}
		
		// Set reg_date
		$user_info['reg_date'] = date('Y-m-d H:i:s');
		
		// Build INSERT statement based on what's in $user_info
		$params = array();
		$comma = $set = $values = '';
		foreach ($user_info as $field => $value) {
			$set .= $comma.$field;
			$values .= $comma.':'.$field;
			$params[":{$field}"] = $value;
			$comma = ',';
		}
		$sql = "INSERT INTO users ({$set}) VALUES ({$values})";
		
		Debug('Reg INSERT param array', $params);
		Debug('Reg INSERT stmnt', $sql);
		
		if (!$this->db->pdo_query($sql, $params, 'alter', 'Error'))
			return false;
		else return $this->db->last_insert_id();
		
	}
	
	/*
	* FUNCTION localLogin($email, $password)
	*	- Login user with local authentication
	*	- Requires $email and $password
	***/
	public function localLogin($email, $password) {
		$sql = "SELECT id, password FROM users WHERE email = '{$email}'";
		
		if (!$result = $this->db->pdo_query($sql, false, 'select_one', ''))
			return false;
		
        if (password_verify($password, $result['password']))
			return $result['id'];
		
		return false;
	}
	
	/*
	* FUNCTION fbLogin($fb_info)
	*	- Login user with Facebook
	*	- $fb_info is array of user info retrieved from Facebook API
	***/
	public function fbLogin($fb_info) {

		// Check if user exists in DB. If so, get local ID and login
		$sql = "SELECT id FROM users WHERE fb_id = {$fb_info['fb_id']}";
        
		Debug('Check if user exists', $sql);
        
		if ($result = $this->db->pdo_query($sql, false, 'select_one', 'Error'))
			return $result['id'];

		// If user does not exist, register using Facebook info
		$user_info['fb_id'] = $fb_info['fb_id'];
		$user_info['email'] = $fb_info['fb_email'];
		$user_info['first_name'] = $fb_info['fb_first_name'];
		$user_info['last_name'] = $fb_info['fb_last_name'];
			
		if ($new_id = $this->registerNew($user_info, true))
	       	return $new_id;

		
		return false;
	}	
}
?>