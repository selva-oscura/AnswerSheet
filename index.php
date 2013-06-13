<?php
	require("index_process.php");
	if((isset($_SESSION['logged_in']))&&($_SESSION['user']['user_level']==1))
	{
		header("location: answersheet.php");
	}
	if((isset($_SESSION['logged_in']))&&($_SESSION['user']['user_level']==2))
	{
		header("location: answersheet_instructor_view.php");
	}
?>

<html>
	<head>
		<title>CodingDojo - AnswerSheet - Login</title>
		<link rel="stylesheet" type="text/css" href="css/index.css">
		<link rel="shortcut icon" href="img/favicon.ico">
		<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$('#login').submit(function(){
					$.post(
						$(this).attr('action'),
						$(this).serialize(),
						function(data)
						{
							$('#login_errors').html(data);						
						}
						, "json"
					);
					return false;
				});
			});
		</script>
	</head>
	<body>
		<div id="wrapper">
			<div id="banner">
				<img src="img/coding_dojo_white.png" />
				<h1>AnswerSheet</h1>
			</div><!--end of div banner-->
			<div id="body">
				<div id="login_space">
					<h2>Please Log In</h2>
					<form id='login' action="index_process.php" method="post">
						<input type="hidden" name="action" value="login" />
						<p>E-mail address:</p>
						<input type="text" name="email" /><br />
						<p>Password:</p>
						<input type="password" name="password" /><br />
						<input type="submit" value="Log In" />
					</form>
					<div id='login_errors'></div>			
				</div><!--end of div login-->
			</div><!--end of div body-->
		</div><!--end of div wrapper-->
	</body>
</html>