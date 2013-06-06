<?php
include_once("connection.php");
session_start();
class Process
{
	var $connection;
	var $data;

	public function __construct()
	{
		$this->connection = new Database();
	}

	function loginAction()
	{
		//check if the email and the password is valid
		$query = "SELECT * FROM users WHERE email = '{$_POST['email']}' AND password ='".md5('coding88' . $_POST['password'] . 'y15u01vb0')."'";
		$users = $this->connection->fetch_all($query);
		if(count($users)>0)
		{
			$_SESSION['logged_in'] = true;
			$_SESSION['user']['id'] = $users[0]['id'];
			$_SESSION['user']['first_name'] = $users[0]['first_name'];
			$_SESSION['user']['last_name'] = $users[0]['last_name'];
			$_SESSION['user']['email'] = $users[0]['email'];
			$_SESSION['user']['cohort'] = $users[0]['cohort'];
			$_SESSION['user']['user_level'] = $users[0]['user_level'];
			$data='<script type="text/javascript">parent.window.location.reload(true);</script>';
			return $data;
		}
		else
		{
			$data="<p class='errors'>The e-mail and/or password that you entered do not match our records.  Please re-enter your information.</p>";
			// $query="INSERT INTO users (email, password, created_at) VALUES ('stlouis_c@yahoo.com', '".md5('coding88' . $_POST['password'] . 'y15u01vb0')."', NOW())";
			// echo $query;
			return $data;
		}
	}
}

if(isset($_POST['action']) and $_POST['action'] == "login")
{
	$my_process = new Process();
	echo json_encode($my_process->loginAction());
}
// elseif(isset($_POST['action']) and $_POST['action'] == "register")
// {
// 	$my_process = new Process();
// 	echo json_encode($my_process->registerAction());
// }
?>