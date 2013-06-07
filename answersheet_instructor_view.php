<?php
	session_start();
	// if(!isset($_SESSION['logged_in']))
	// {
	// 	header("location: index.php");
	// }
	// if((isset($_SESSION['logged_in']))&&($_SESSION['user']['user_level']==1))
	// {
	// 	header("location: answersheet.php");
	// }
	include('answersheet_process.php');
?>
<html>
	<head>
		<title>CodingDojo - AnswerSheet - Instructor View</title>
		<link rel="stylesheet" type="text/css" href="css/answersheet.css" />
	</head>
	<body>
		<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
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
<?php 
				echo "<div id='banner'>
					<img src='img/coding_dojo_white.png' />
					<h1>AnswerSheet</h1>
					<div class='landing'>
						<p><a href='logout_process.php'>Log Off</a></p>
					</div>
					<div class='landing'>
						<p>Welcome, " . $_SESSION['user']['first_name'] ."! </p>
					</div>
				</div><!--end of div banner-->";
?>
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
