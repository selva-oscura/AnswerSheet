<?php 
include("connection.php");
// session_start();

class Process
{
	var $connection;
	public function __construct(){
		$this->connection = new Database();
	}
}
	
$process = new Process();


Class Display extends Process
{
	function recentAdditions()
	{
		$recent_additions=array();
		$query="SELECT schedules.week, schedules.day, schedules.day_theme, feedback_title , feedback_author, DATE(available_on) AS available_date, users.first_name AS first_name FROM answers LEFT JOIN schedules on answers.schedule_id=schedules.id LEFT JOIN users ON users.id=answers.feedback_author WHERE (((answers.ind_or_cohort=1) AND (answers.recipient={$_SESSION['user']['id']})) OR ((answers.ind_or_cohort=2) AND (answers.recipient={$_SESSION['user']['cohort']}))) AND ((answers.available_on >= NOW()-INTERVAL 3 DAY) AND (answers.available_on <= NOW())) ORDER BY answers.available_on DESC";
		$recent_additions = $this->connection->fetch_all($query);


		if(!(count($recent_additions)>0))
		{
			$html="<p>No new answers posted in the last 72 hours.</p>";
		}
		else
		{
			$html="<ul>";
			foreach($recent_additions as $recent_addition)
			{
				$html=$html . "<li>Week " . $recent_addition['week'] . ", Day " . $recent_addition['day'] . " - " . $recent_addition['day_theme'] . " - " . $recent_addition['feedback_title'] . " - posted by " . $recent_addition['first_name'] . " - " . $recent_addition['available_date'] . "</li>";
			}
			$html=$html . "</ul>";
		}
		echo $html;
	}

	function answerSheet()
	{
		$answers=array();
		$vid=0;
		$html="";
		$query="SELECT schedules.id AS schedule_id, week, day, day_theme FROM schedules ORDER BY week, day ASC";
		$weeks_days_themes = $this->connection->fetch_all($query);
		$query="SELECT schedule_id, feedback_title, feedback_author, DATE(available_on) AS available_date, feedback_type, url, users.first_name AS first_name FROM answers LEFT JOIN schedules on schedules.id=answers.schedule_id LEFT JOIN users on users.id=answers.feedback_author WHERE (((answers.ind_or_cohort=1) AND (answers.recipient={$_SESSION['user']['id']})) OR ((answers.ind_or_cohort=2) AND (answers.recipient={$_SESSION['user']['cohort']}))) AND (answers.available_on <= NOW()) ORDER BY schedule_id, available_date ASC";
		$answers = $this->connection->fetch_all($query);
		if(!(count($answers)>0))
		{
			$html="<p>No answers have been posted yet.  Please check back soon.</p>";
		}
		else
		{
			$week=0;
			$day=0;
			foreach($weeks_days_themes as $week_day_theme){
				if($week_day_theme['week']>$week)
				{
					$html=$html ."<h3 id='week" . $week_day_theme['week'] . "' class='week_bar'><img class='a_right' src='img/arrow_right_white.png' /><img class='a_down' src='img/arrow_down_white.png'/>Week " . $week_day_theme['week'] . "</h3>";
					$week=$week_day_theme['week'];
				}
				if(!($week_day_theme['day']==$day))
				{
					$html=$html ."<div class='week" . $week_day_theme['week'] . "'><p class='day_bar wk" . $week_day_theme['week'] . "'>Day " . $week_day_theme['day'] . " - " . $week_day_theme['day_theme'] . "</p>";
					$day=$week_day_theme['day'];
					$assignment_count=0;
					foreach($answers as $answer)
					{
						if($week_day_theme['schedule_id']==$answer['schedule_id'])
						{
							$assignment_count++;
							$html=$html . "<table class='assignment_space'><tbody><tr><td>" . $answer['feedback_title'] . " from " . $answer['first_name'];
							if($answer['feedback_type']==1)
							{
								$html=$html . ", video, " . $answer['available_date'] ."</td><td class='get_media'><a href='media/" . $answer['url'] . "' class='vid" . $vid . "' target='vid" . $vid . "'>View</td></tr></tbody></table><iframe name='vid" . $vid . "'id='vid" . $vid . "' frameborder='0' allowfullscreen></iframe>";
								$vid++;
							}
							else
							{
								$html=$html . ", code, " . $answer['available_date'] ."</td><td class='get_media'><a href='media/" . $answer['url'] . "'>Download</a></td></tr></tbody></table>";
							}							
						}
					}
					if($assignment_count==0)
					{
						$html=$html . "<table class='assignment_space'><tbody><tr><td>No answers or feedback for this day's assignments</td></tr></tbody></table>";
					}
					$html=$html . "</div><!--end of day " . $day . "-->";
				}
			}
		}
		echo $html;
	}

	function cohortDropdown()
	{
		
		$query="SELECT cohorts.id as cohort_id, location, start_date from cohorts WHERE cohorts.id>1 ORDER BY cohorts.start_date ASC";
		$results = $this->connection->fetch_all($query);
		$html = "<div id='cohort_select'>
			<form id='display_cohort' action='answersheet_process.php' method='post'>
			<select name = 'cohort'>
			<option value=''></option>";
		foreach($results as $cohort)
		{
			$html=$html . "<option value=" . $cohort['cohort_id'] . ">" . $cohort['location'] . " - " . $cohort['start_date'] . "</option>";
		}
		$html=$html . "</select></form></div>";
		// eliminated after AJAXing of submit
		// $html=$html . "<input type='submit' id='button' value='Display' />";  
		echo $html;
	}

	function cohortPlusInstructorsDropdown()
	{
		
		$query="SELECT cohorts.id as cohort_id, location, start_date from cohorts ORDER BY cohorts.location, cohorts.start_date ASC";
		$results = $this->connection->fetch_all($query);
		$html = "
			<select name = 'cohort'>
			<option value=''></option>";
		foreach($results as $cohort)
		{
			$html=$html . "<option value=" . $cohort['cohort_id'] . ">" . $cohort['location'] . " - " . $cohort['start_date'] . "</option>";
		}
		$html=$html . "</select></form>";
		// eliminated after AJAXing of submit
		// $html=$html . "<input type='submit' id='button' value='Display' />";  
		echo $html;
	}






	function editAnswers()
	{
		$cohort=$_POST['cohort'];
		$weeks_days_themes=array();
		$answers=array();
		$students=array();
		$html="";
		$query="SELECT schedules.id AS schedule_id, week, day, day_theme FROM schedules WHERE schedules.cohort_id={$_POST['cohort']} ORDER BY week, day ASC";
		$weeks_days_themes = $this->connection->fetch_all($query);
		$query="SELECT answers.id AS answer_id, schedule_id, feedback_title, feedback_author, ind_or_cohort, recipient AS recipient_id, DATE(available_on) AS available_date, available_on, feedback_type, url, users.first_name AS author_first_name, users.id AS authors_id, users.cohort_id FROM answers LEFT JOIN schedules on schedules.id=answers.schedule_id LEFT JOIN users on users.id=answers.feedback_author WHERE (((answers.ind_or_cohort=1) AND (answers.recipient IN (SELECT users.id FROM users WHERE cohort_id={$_POST['cohort']}))) OR ((answers.ind_or_cohort=2) AND (answers.recipient={$_POST['cohort']}))) AND (answers.available_on <= NOW()) ORDER BY schedule_id, available_date ASC";
		// $html= $query;
		$answers = $this->connection->fetch_all($query);
		$query="SELECT users.id AS user_id, first_name, last_name FROM users WHERE cohort_id={$_POST['cohort']}";
		$students=$this->connection->fetch_all($query);
		if(!(count($answers)>0))
		{
			$html="<p>No feedback or answers have been posted yet.</p>";
		}
		else
		{
			$week=0;
			$day=0;	
			foreach($weeks_days_themes as $week_day_theme){
				if($week_day_theme['week']>$week)
				{
					$html=$html ."<h3 id='week" . $week_day_theme['week'] . "' class='week_bar'>Week " . $week_day_theme['week'] . "</h3>";
					$week=$week_day_theme['week'];
				}
				if(!($week_day_theme['day']==$day))
				{
					$html=$html ."<div class='week" . $week_day_theme['week'] . "'><p class='day_bar wk" . $week_day_theme['week'] . "'>Day " . $week_day_theme['day'] . " - " . $week_day_theme['day_theme'] . "</p>";
					$day=$week_day_theme['day'];
					$schedule_id=$week_day_theme['schedule_id'];
					$assignment_count=0;
					foreach($answers as $answer)
					{
						if($schedule_id==$answer['schedule_id'])
						{
							$assignment_count++;
							//Form for updating answer
							if((isset($_POST['edit_answer'])) && (($answer['answer_id'])==($_POST['answer_id'])))
							{
								$html=$html . "<table class='assignment_space'><tbody><tr><td>
									<fieldset class='form_align'>
									<form class='udpate_answer' action='answersheet_process.php' method='post'>
									<input type='hidden' name='update_answer' />";
// <input type='hidden' name='feedback_author' value='" . $_SESSION['user']['user_id'] . "'/> 
								$html=$html . "<input type='hidden' name='cohort' value='". $cohort . "' />
									<input type='hidden' name='answer_id' value='" . $answer['answer_id'] . "' />
									<label>Feedback Title</label><input type='text' name='feedback_title' value='".$answer['feedback_title']."' /><br />
									<label>Recipient</label><input type='text' name='recipient' value='".$answer['recipient_id']."' /><br />
									<label>Date and time available</label><input id='datetimepicker' name='available_on' value='".$answer['available_on']."' /><br />";
								if($answer['feedback_type']==1)
								{
									$html=$html . "<label>Feedback Type</label><input type='radio' name='feedback_type' value='1' checked>Video
									<input type='radio' name='feedback_type' value='2'>Code<br />";
								}
								else

								{
									$html=$html . "<label>Feedback Type</label><input type='radio' name='feedback_type' value='1'>Video
									<input type='radio' name='feedback_type' value='2' checked>Code<br />";
								}
								$html=$html . "<label>URL</label><input type='text' name='url' value='".$answer['url']."'><br />
									<input type='submit' class='udpate_button' value='Update Answer' />
									</form></fieldset>
									</td></tr></tbody></table>";
							}
							//Display feedback
							else
							{
								$html=$html . "<table class='assignment_space'><tbody><tr><td>" . $answer['feedback_title'] . " posted by " . $answer['author_first_name'];
								if($answer['ind_or_cohort']==1)
								{
									foreach($students as $student){
										if($student['user_id']==$answer['recipient_id'])
										{
											$html=$html . " for " . $student['first_name'] . " " . $student['last_name'];
										}
									}
								}
								if($answer['feedback_type']==1)
								{
									$html=$html . ", video, ";
								}
								else
								{
									$html=$html . ", code, ";
								}
								$html=$html . $answer['available_date'] . "</td>";
								//Form for requesting form to update answer 
								$html=$html . "<td class='right'>
									<form class='edit_answer' action='answersheet_process.php' method='post'>
									<input type='hidden' name='edit_answer' />
									<input type='hidden' name='cohort' value='" . $cohort . "' />	
									<input type='hidden' name='answer_id' value='" . $answer['answer_id'] . "' />
									<input type='submit' class='edit_button' value='Edit' />
									</form>";
								//Form to delete answer 
								$html=$html . "
									<form class='delete_answer' action='answersheet_process.php' method='post'>
									<input type='hidden' name='delete_answer' />
									<input type='hidden' name='cohort' value='" . $cohort . "'/>	
									<input type='hidden' name='answer_id' value='" . $answer['answer_id'] . "'/>
									<input type='submit' class='delete_button' value='Delete' />
									</form></td>
									</tr></tbody></table>";
							}							
						}
					}							
					if($assignment_count==0)
					{
						$html=$html . "<table class='assignment_space'><tbody><tr><td>No answers or feedback for this day's assignments</td></tr></tbody></table>";
					}
					//Form for creating another answer 
					if((isset($_POST['new_answer'])) && (($week_day_theme['schedule_id'])==($_POST['schedule_id'])))
					{
						$html=$html . "<table class='assignment_space'><tbody><tr><td>
							<fieldset class='form_align'>
							<form class='create_answer' action='answersheet_process.php' method='post'>
							<input type='hidden' name='create_answer' />";
// <input type='hidden' name='feedback_author' value='" . $_SESSION['user']['user_id'] . "'/> 
						$html=$html . "<input type='hidden' name='cohort' value='" . $_POST['cohort'] . "' />
							<input type='hidden' name='schedule_id' value='" . $_POST['schedule_id'] . "' />
							<label>Feedback Title: </label><input type='text' name='feedback_title' /><br />
							<label>Recipient: </label><select name = 'recipient'><option value='0'>entire cohort</option>";
						foreach($students as $student)
						{
							$html=$html . "<option value=" . $student['user_id'] . ">" . $student['first_name'] . " " . $student['last_name'] . "</option>";
						}
						$html=$html . "</select><br />
							<label>Date and time available: </label><input id='datetimepicker' name='available_on'/><br />
							<label>Feedback Type: </label><input type='radio' name='feedback_type' value='1'>Video
							<input type='radio' name='feedback_type' value='2'>Code<br />
							<input type='submit' class='create_button' value='Submit feedback' />
							</form>
							</fieldset></td></tr></tbody></table>";
						$html=$html . "</div><!--end of day " . $day . "-->";
					}
					//Form requesting the form to create another answer
					else
					{
						$html=$html . "<table class='assignment_space'><tbody><tr><td>
							<form class='new_answer' action='answersheet_process.php' method='post'>
							<input type='hidden' name='new_answer' />
							<input type='hidden' name='cohort' value='" . $cohort . "' />	
							<input type='hidden' name='schedule_id' value='" . $schedule_id . "' />
							<input type='submit' class='new_button' value='Add feedback' />
							</form>
							</td></tr></tbody></table>";
						$html=$html . "</div><!--end of day " . $day . "-->";
					}
				}
			}
		}
		echo json_encode($html);
	}
}

Class Edit extends Process
{

	function searchUser(){
		$query = "SELECT first_name, last_name, cohort_id, cohorts.location, cohorts.start_date FROM users LEFT JOIN cohorts on cohorts.id=users.cohort_id WHERE (first_name LIKE '{$_POST['name']}%' OR last_name LIKE '{$_POST['name']}%') ORDER BY last_name ASC";
		$users = $this->connection->fetch_all($query);
		$html = "<table id='user_table' border='1'>
			<thead>
				<tr>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Location</th>
					<th>Date of Cohort</th>
				</tr>
			</thead>
			<tbody>
		";
		foreach($users as $user)
		{
			$html .= "
				<tr>
					<td>{$user['first_name']}</td>
					<td>{$user['last_name']}</td>";
			if($user['cohort_id']==1){
				$html .= "<td>All</td>
					<td>Instructor</td>
				</tr>
				";
			}
			else{
			$html .= "<td>{$user['location']}</td>
					<td>{$user['start_date']}</td>
				</tr>
				";				
			}
		}
		$html .= "
				</tbody>
			</table>
		";
		$data['html'] = $html;
		echo json_encode($data);
		// echo json_encode($html);
	}

	function createCohort(){
		$query="INSERT INTO cohorts (location, start_date, created_at) VALUES ('{$_POST['location']}', '{$_POST['start_date']}', NOW())";
		mysql_query($query);
		$query="SELECT * from cohorts WHERE location='{$_POST['location']}' AND start_date='{$_POST['start_date']}'";
		$cohorts = $this->connection->fetch_all($query);
		foreach($cohorts as $cohort)
		{
			foreach($cohort as $key => $value)
			{
				if($key=='id')
				{
					$cohort_id=$value;
				}
			}
		}

		for($week=1; $week<=9; $week++)
		{
			for($day=1; $day<=5; $day++)
			{
				$query="INSERT INTO schedules (cohort_id, week, day, created_at) VALUES ($cohort_id, $week, $day, NOW())";
				mysql_query($query);
			}
		}
	}
}


$display = new Display;

if(isset($_POST['new_answer']))
{
	$display->editAnswers();
	unset($_POST);
}

if(isset($_POST['edit_answer']))
{
	$display->editAnswers();
	unset($_POST);
}

if(isset($_POST['delete_answer']))
{

	$query="DELETE FROM answers WHERE answers.id={$_POST['answer_id']}";
	mysql_query($query);
	$display->editAnswers();
	unset($_POST);
}

if(isset($_POST['cohort']))
{
	$display->editAnswers();
	unset($_POST);	
}

if(isset($_POST['create_answer']))
{
var_dump($_POST);
	unset($_POST);
	// $query="INSERT INTO answers (feedback_title, feedback_author, recipient, available_on, feedback_type, url) VALUES ('{$_POST['feedback_title']}', '{$_POST['feedback_author']}', '{$_POST['recipient']}', '{$_POST['available_on']}', '{$_POST['feedback_type']}', '{$_POST['url']}')";
	// $display->editAnswers();
	// unset($_POST);
}

if(isset($_POST['update_answer']))
{

var_dump($_POST);
	unset($_POST);
	// $query="UPDATE answers SET feedback_title='{$_POST['feedback_title']}', feedback_author='{$_POST['feedback_author']}', recipient='{$_POST['recipient']}', available_on='{$_POST['available_on']}', feedback_type='{$_POST['feedback_type']}', url='{$_POST['url']}' WHERE answers.id='{$_POST['answer_id']}'";
	// $display->editAnswers();
	// unset($_POST);
}




$edit = new Edit;

if(isset($_POST['add_cohort']))
{
	$edit->createCohort();
	unset($_POST);
}

if(isset($_POST['search_users']))
{
	$edit->searchUser();
	unset($_POST);
}

?>