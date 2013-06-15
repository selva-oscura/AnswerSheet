<?php
	include('answersheet_process.php');
	if(!isset($_SESSION['logged_in']))
	{
		header("location: index.php");
	}
	if((isset($_SESSION['logged_in']))&&($_SESSION['user']['user_level']==1))
	{
		header("location: answersheet.php");
	}
?>
<html>
	<head>
		<title>CodingDojo - AnswerSheet - Administrator View</title>
		<link rel="stylesheet" type="text/css" href="css/answersheet.css" />
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<link rel="shortcut icon" href="img/favicon.ico">
		<script src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
		<script>
			$(document).ready(function() 
		    {
				$("#datepicker").datepicker({
					dateFormat: 'yy-mm-dd', 
					numberOfMonths: 3,
					showButtonPanel: true,
					value:new Date()
				});

				$('#search_by_cohort').submit(function(){
					$.post(
						$(this).attr('action'),
						$(this).serialize(),
						function(data){
							$('#results').html(data.html);
						},
						"json"
					);
					return false;
				});

				$('#search_text').keyup(function(){
					$('#search_users').submit();
				});
				$('#search_users').submit(function(){
					$.post(
						$(this).attr('action'),
						$(this).serialize(),
						function(data){
							$('#results').html(data.html);
						},
						"json"
					);
					return false;
				});

				// $('#search_users').submit();

				$('#display_cohort').change(function(){
					$('#display_cohort').submit();
				});
		  		$('#display_cohort').submit(function(){
					$.post(
						$(this).attr('action'),
						$(this).serialize(),
						function(data)
						{
							$('#cohort_display').html(data);
						}
						, "json"
					);
					return false;
				}); 
			});
		</script>
	</head>
	<body>
		<div id='reset'>
			<div id='wrapper'>
				<div id='banner'>
					<img src='img/coding_dojo_white.png' />
					<h1>AnswerSheet</h1>
					<div class='landing'>
						<ul>
							<li><a href='logout_process.php'>Log Off</a></li>
							<li><a href='answersheet_instructor_view.php'>Instructor's Page</a> </li>
							<li>
<?php 
						echo "Welcome, " . $_SESSION['user']['first_name'] ."!";
?>
							</li>
						</ul>
					</div>
				</div><!--end of div banner-->
				<div id='body'>
				<h2>Users</h2>
				<div class='inset'>
					<p>Add a new user</p>
					<div class='form_align'>
						<form class='add_user' action='answersheet_process.php' method='post'>
							<input type='hidden' name='add_user' />
							<label>First Name:</label><input type='text' name='first_name' /><br />
							<label>Last Name:</label><input type='text' name='last_name' /><br />
							<label>E-mail address:</label><input type='text' name='email' /><br />
							<label>Cohort:</label>
<?php 
							$display->cohortPlusInstructorsDropdown();
?>

							<label>Start Date:</label><input id='datepicker' name='start_date'/><br />
							<input type='submit' id='button' value='Add a New Cohort' />
						</form>
					</div><!--end of div form_align-->



					<p>Edit or delete an existing user</p>
					<p>Search for user by cohort</p>
					<div class='form_align'>
						<!-- <div id='cohorts'> -->
						<form id='search_by_cohort' action='answersheet_process.php' method='post'>
							<label></label>

<?php 
					$display->cohortPlusInstructorsDropdown();
?>
						</form>
 						<!-- </div>end of div cohorts -->
					<div id='cohort_display' class='clear'></div>
 					</div><!--end of div form_align-->
					<p>Search for user by name</p>
					<div class='form_align'>
						<form id="search_users" action="answersheet_process.php" method="post">
							<input type='hidden' name='search_users' />
							<label>Name: </label><input id="search_text" type="text" name="name" />
							<input type="submit" value="Submit" />
						</form>
					</div><!--end of div form_align-->
					<div id="results"></div>



				</div><!--end of div inset-->
				<h2>Cohorts and Course Schedule</h2>
				<div class='inset'>
					<p>Add a new cohort</p>
					<div class='form_align'>
						<form class='add_cohort' action='answersheet_process.php' method='post'>
							<input type='hidden' name='add_cohort' />
							<label>Location:</label><input type='text' name='location' /><br />
							<label>Start Date:</label><input id='datepicker' name='start_date'/><br />
							<input type='submit' id='button' value='Add a New Cohort' />
						</form>
					</div><!--end of div form_align -->
					<p>Edit or delete an existing cohort</p>
					<p>Edit the schedule of an existing cohort</p>
				</div><!--end of div inset-->



				</div><!--end of div body-->
			</div><!--end of div wrapper-->
		</div><!--end of div reset-->
		<div style='display:none;' id="dialog-confirm" title="Delete this answer?">
			<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>This assignment feedback will be permanently deleted and cannot be recovered. Are you sure?</p>
		</div>
	</body>
</html>
