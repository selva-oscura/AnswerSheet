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
		<script src="http://code.jquery.com/jquery-1.8.2.js"></script>
		<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
		<script>
			$(document).ready(function() 
		    {
				$("#datepicker").datepicker({
					dateFormat: 'yy-mm-dd', 
					numberOfMonths: 3,
					showButtonPanel: true,
					value:new Date()
				});

				$('#add_user').submit(function(){
					$.post(
						$(this).attr('action'),
						$(this).serialize(),
						function(data){
							$('#add_user_response').html(data);
						},
						"json"
					);
					return false;
				});

				$('#search_by_cohort').change(function(){
					$('#search_by_cohort').submit();
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

				// $('#search_text').keyup(function(){
				// 	console.log;
				// 	$('#search_users').submit();
				// });
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

				$('#add_cohort').submit(function(){
					$.post(
						$(this).attr('action'),
						$(this).serialize(),
						function(data){
							$('#add_cohort_response').html(data);
						},
						"json"
					);
					return false;
				});

				$('#select_cohort_schedule').submit(function(){
					$.post(
						$(this).attr('action'),
						$(this).serialize(),
						function(data){
							$('#show_cohort_schedule').html(data);
						},
						"json"
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
					<div class='cat_header'><h2>Users</h2></div>
					<div class='col_1'>
						<div class='inset'>
							<p>Add a new user</p>
							<div class='form_align'>
								<form id='add_user' action='answersheet_process.php' method='post'>
									<input type='hidden' name='add_user' />
									<label>First Name:</label><input type='text' name='first_name' /><br />
									<label>Last Name:</label><input type='text' name='last_name' /><br />
									<label>E-mail address:</label><input type='text' name='email' /><br />
									<label>Cohort:</label>
<?php 
									$display->cohortPlusInstructorsDropdown();
?>

									<input type='submit' id='button' value='Add this User' />
								</form>
								<div id='add_user_response'></div>
							</div><!--end of div form_align-->
						</div><!--end of div inset-->
					</div><!--end of div col_1-->



					<div class='col_2'>
						<div class='inset'>
							<p>Edit or delete an existing user</p>
							<ul>
								<li>Search for user by cohort</li>
							</ul>
							<div class='form_align'>
								<form id='search_by_cohort' action='answersheet_process.php' method='post'>
									<input type='hidden' name='search_by_cohort' />
									<label></label>

<?php 
									$display->cohortPlusInstructorsDropdown();
?>
									<input type="submit" value="Submit" />
								</form>
		 					</div><!--end of div form_align-->
		 					<ul>
								<li>Search for user by name</li>
							</ul>
							<div class='form_align'>
								<form id="search_users" action="answersheet_process.php" method="post">
									<input type='hidden' name='search_users' />
									<label>Name: </label><input id="search_text" type="text" name="name" />
									<input type="submit" value="Submit" />
								</form>
							</div><!--end of div form_align-->
						</div><!--end of div inset-->
					</div><!--end of div col_2-->
					<div class='clear'></div>
					<div id="results"></div>
					
					<div class='cat_header'><h2>Cohorts and Course Schedule</h2></div>
					<div class='col_1'>
						<div class='inset'>
							<p>Add a new cohort</p>
							<div class='form_align'>
								<form id='add_cohort' action='answersheet_process.php' method='post'>
									<input type='hidden' name='add_cohort' />
									<label>Location:</label><input type='text' name='location' /><br />
									<label>Start Date:</label><input id='datepicker' name='start_date'/><br />
									<input type='submit' id='button' value='Add a New Cohort' />
								</form>
							</div><!--end of div form_align -->
							<div id='add_cohort_response'></div>
						</div><!--end of div inset-->
					</div><!--end of div class='col_1'-->
					<div class='col_2'>
						<div class='inset'>
							<p>Edit the schedule of an existing cohort</p>
							<div class='form_align'>
								<form id='select_cohort_schedule' action='answersheet_process.php' method='post'>
									<input type='hidden' name='select_cohort_schedule' />
									<label></label>	
<?php 
					$display->cohortDropdown();
?>
									<input type="submit" value="Submit" />
								</form>

	 						</div><!--end of div form_align-->
	 					</div><!--end of div inset-->
					</div><!--end of div class='col_2'-->
					<div class='clear'></div>
					<div id='show_cohort_schedule'></div>
					<div class='col_1'>
						<div class='inset'>
							<p>Edit or delete an existing cohort</p>
						</div><!--end of div inset-->
					</div><!--end of div class='col_1'-->
					<div class='clear'></div>					
				</div><!--end of div body-->
			</div><!--end of div wrapper-->
		</div><!--end of div reset-->
	</body>
</html>
