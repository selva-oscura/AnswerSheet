<?php
	session_start();
	// if(!isset($_SESSION['logged_in']))
	// {
	// 	header("location: index.php");
	// }
	// if((isset($_SESSION['logged_in']))&&($_SESSION['user']['user_level']==2))
	// {
	// 	header("location: answersheet_instructor_view.php");
	// }
	include('answersheet_process.php');
?>
<html>
	<head>
		<title>CodingDojo - AnswerSheet</title>
		<link rel="stylesheet" type="text/css" href="css/answersheet.css" />
		<link rel="shortcut icon" href="img/favicon.ico">
		<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
		<script>
			$(document).ready(function() 
		    { 
		    	$('#feedback_accordion div').hide();
		    	$('.a_down').hide();
		    	$('#feedback_accordion h3').click(function(){
		    		$('#feedback_accordion div').hide();
		    		$('#feedback_accordion img').hide();
		    		$('.a_right').show();
		    		$('.'+$(this).attr('id')).show();
		    		$($(this).children()[0]).hide();
		    		$($(this).children()[1]).show();
		    	});
		    	$('iframe').hide();
		    	$('.get_media a').click(function(e){
		    		$('iframe').hide();
		    		$('#'+$(this).attr('class')).show();
		    	});
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
					<div id='updates'>
						<h3>Recent Additions:</h3>
						<?php 
							$display->recentAdditions();
						?>
					</div>			
					<div id='feedback_accordion' class="clear">
						<?php
							$display->answerSheet();
						?>
					</div><!--end of div feedback_accordion-->
				</div><!--end of div body-->
			</div><!--end of div wrapper-->
		</div><!--end of div reset-->
	</body>
</html>
