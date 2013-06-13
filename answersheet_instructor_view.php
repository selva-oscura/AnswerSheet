<?php
	session_start();
	if(!isset($_SESSION['logged_in']))
	{
		header("location: index.php");
	}
	if((isset($_SESSION['logged_in']))&&($_SESSION['user']['user_level']==1))
	{
		header("location: answersheet.php");
	}
	include('answersheet_process.php');
?>
<html>
	<head>
		<title>CodingDojo - AnswerSheet - Instructor View</title>
		<link rel="stylesheet" type="text/css" href="css/answersheet.css" />
		<link href="css/kendo.common.min.css" rel="stylesheet" />
		<link href="css/kendo.uniform.min.css" rel="stylesheet" />
		<link rel="shortcut icon" href="img/favicon.ico">
		<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
		<script src="js/jquery.min.js"></script>
		<script src="js/kendo.web.min.js"></script>
		<script>
			$(document).ready(function() 
		    {
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
				$(document).on('click', '.delete_button', function(event){
					$('.delete_answer').on('submit', function(){
						$.post(
							$(this).attr('action'),
							$(this).serialize(),
							function(data)
							{
								$('#cohort_display').html(data);
							}
							, "json"
						)
						return false;					
					});
				});
				$(document).on('click', '.new_button', function(event){
					$('.new_answer').on('submit', function(){
						$.post(
							$(this).attr('action'),
							$(this).serialize(),
							function(data)
							{
								$('#cohort_display').html(data);
							}
							, "json"
						)
						return false;					
					});
				});
				$(document).on('click', '.create_button', function(event){
					$('.create_answer').on('submit', function(){
						$.post(
							$(this).attr('action'),
							$(this).serialize(),
							function(data)
							{
								$('#cohort_display').html(data);
							}
							, "json"
						)
						return false;					
					});
				});
				$(document).on('click', '.edit_button', function(event){
					$('.edit_answer').on('submit', function(){
						$.post(
							$(this).attr('action'),
							$(this).serialize(),
							function(data)
							{
								$('#cohort_display').html(data);
							}
							, "json"
						)
						return false;					
					});
				});
				$(document).on('click', '.update_button', function(event){
					$('.update_answer').on('submit', function(){
						$.post(
							$(this).attr('action'),
							$(this).serialize(),
							function(data)
							{
								$('#cohort_display').html(data);
							}
							, "json"
						)
						return false;					
					});
				});

				// create DateTimePicker from input HTML element
				$(document).on('click', '#datetimepicker', function (){
	                $('#datetimepicker').kendoDateTimePicker({
	                	format: 'yyyy/MM/dd HH:mm:ss', 
	                	timeFormat: 'HH:mm',
	                    value:new Date()
	                });
	            });


		    	// $('#cohort_display div').hide();
		    	// $('.a_down').hide();
		    	// $('#cohort_display h3').click(function(){
		    	// 	$('#cohort_display div').hide();
		    	// 	$('#cohort_display img').hide();
		    	// 	$('.a_right').show();
		    	// 	$('.'+$(this).attr('id')).show();
		    	// 	$($(this).children()[0]).hide();
		    	// 	$($(this).children()[1]).show();
		    	// });

		    	// $('iframe').hide();
		    	// $('.get_media a').click(function(e){
		    	// 	$('iframe').hide();
		    	// 	$('#'+$(this).attr('class')).show();
		    	// });
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
						<p><a href='logout_process.php'>Log Off</a></p>
					</div>
					<div class='landing'>
<?php 
						echo "<p>Welcome, " . $_SESSION['user']['first_name'] ."! </p>";
?>
					</div>
				</div><!--end of div banner-->
				<div id='body'>
<!--  					Choose Location and Cohort -->
<!-- 					<div id='cohort_select'>
						<h3>Select Location and Cohort</h3> -->
						<div id='cohorts'>
<?php 
							$display->cohortDropdown();
?>
	 					</div>		
<!-- 	 				</div> --><!--end of div cohort select-->
	 				<div id='cohort_display' class='clear'></div>
				</div><!--end of div body-->
			</div><!--end of div wrapper-->
		</div><!--end of div reset-->
	</body>
</html>
