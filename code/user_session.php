<?php
	require_once('db-connect.php');
	
	$username = $_POST['username'];
	$password = $_POST['password'];

	$user_check = check_username($username,$db);
	$password_check = check_password($password,$db);
	$business_name = get_business_name($username,$db);

	if(empty($password)){

		echo "Please go back and enter a password";
		die();

	}

	if(empty($username)){

		echo "Please go back and enter a username";
		die();

	}

	if($user_check[0]['username'] == $username && $password_check[0]['password'] == $password){
		session_start();
		$_SESSION['user_id'] = $business_name[0]['business_name'];
		$ip = $_SERVER['REMOTE_ADDR'];
		date_default_timezone_set('America/Los_Angeles');
		$date = date('l jS \of F Y h:i:s A');
		$login_sql = 'INSERT INTO `logins` VALUES("","'.$user_check[0]['username'].'","'.$ip.'","'.$date.'")';
		$login_query = $db->query($login_sql);


		$current_user = $_SESSION['user_id'];
		$sql = 'SELECT `role` FROM `users` WHERE `business_name` = "'.$current_user.'"';
		$query = $db->query($sql);
		$user_check = $query->fetchAll(PDO::FETCH_ASSOC);
		if(!isset($_SESSION['admin'])){
			if($user_check[0]['role'] == "admin"){
				$_SESSION['admin'] = "admin";
			}
		}

		$ml_sql = 'SELECT * FROM `business` WHERE `business_name` = "'.$business_name[0]['business_name'].'"';
		$ml_query = $db->query($ml_sql);
		$ml_result = $ml_query->fetchAll(PDO::FETCH_ASSOC);

		if($ml_result[0]['is_parent_company'] == 'yes'){
			$_SESSION['multi_location'] = $business_name[0]['business_name'];
		}


		header('location:../index.php');

	}else{

		echo "Your Username/Password Combo Is Incorrect Please Go Back And Try Again";
		die();

	}
	
	
	

	function check_username($username,$db){
	$sql = 'SELECT `username` FROM `users` WHERE `username` = "'.$username.'"';
	$query = $db->query($sql);
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    return $results;

	}

	function check_password($password,$db){
	$sql = 'SELECT `password` FROM `users` WHERE `password` = "'.$password.'"';
	$query = $db->query($sql);
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    return $results;

	}

	function get_business_name($username,$db){
	$sql = 'SELECT `business_name` FROM `users` WHERE `username` = "'.$username.'"';
	$query = $db->query($sql);
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    return $results;

	}

?>