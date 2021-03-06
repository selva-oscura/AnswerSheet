<?php 
session_start();
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
		$query="SELECT schedules.week, schedules.day, schedules.day_theme, feedback_title , feedback_author, DATE(available_on) AS available_date, users.first_name AS first_name FROM answers LEFT JOIN schedules on answers.schedule_id=schedules.id LEFT JOIN users ON users.id=answers.feedback_author WHERE (((answers.ind_or_cohort=1) AND (answers.recipient={$_SESSION['user']['user_id']})) OR ((answers.ind_or_cohort=2) AND (answers.recipient={$_SESSION['user']['cohort']}))) AND ((answers.available_on >= NOW()-INTERVAL 3 DAY) AND (answers.available_on <= NOW())) ORDER BY answers.available_on DESC";
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
		$query="SELECT schedule_id, feedback_title, feedback_author, DATE(available_on) AS available_date, feedback_type, url, answers.ind_or_cohort AS ind_or_cohort, users.first_name AS first_name FROM answers LEFT JOIN schedules on schedules.id=answers.schedule_id LEFT JOIN users on users.id=answers.feedback_author WHERE (((answers.ind_or_cohort=1) AND (answers.recipient={$_SESSION['user']['user_id']})) OR ((answers.ind_or_cohort=2) AND (answers.recipient={$_SESSION['user']['cohort']}))) AND (answers.available_on <= NOW()) ORDER BY schedule_id, available_date ASC";
		$answers = $this->connection->fetch_all($query);
		$query="SELECT users.id AS user_id, first_name, last_name FROM users WHERE cohort_id={$_SESSION['user']['cohort']}";
		$students=$this->connection->fetch_all($query);
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
							if($answer['ind_or_cohort']==1){
								foreach($students as $student){
									if(($student['user_id'])==$_SESSION['user']['user_id']){
										$html=$html . " for " . $student['first_name'];
									}
								}
							}
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
		// <div id='cohort_select'>
		// 	<form id='display_cohort' action='answersheet_process.php' method='post'>
		$html = "<select name = 'cohort'>
			<option value=''></option>";
		foreach($results as $cohort)
		{
			$html=$html . "<option value=" . $cohort['cohort_id'] . ">" . $cohort['location'] . " - " . $cohort['start_date'] . "</option>";
		}
		$html=$html . "</select>";
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
		$html=$html . "</select>";
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
								// $html. "nada";
								$html=$html . "<table class='assignment_space'><tbody><tr><td>
									<fieldset class='form_align'>
									<form class='udpate_answer' action='answersheet_process.php' method='post'>
									<input type='hidden' name='update_answer' />
									<input type='hidden' name='feedback_author' value='" . $_SESSION['user']['user_id'] . "'/> 
									<input type='hidden' name='cohort' value='". $cohort . "' />
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
									</td>";
								$html=$html . "
									<form class='delete_request' action='answersheet_process.php' method='post'>
									<input type='hidden' name='delete_request' />
									<input type='hidden' name='cohort' value='" . $cohort . "'/>	
									<input type='hidden' name='answer_id' value='" . $answer['answer_id'] . "'/>
									<input type='submit' class='delete_request_button' value='Delete' />
									</form></td>
									</tr></tbody></table>";
							}
							//Display feedback

// I THINK THAT THIS ELSE STATEMENT MAY END IN THE WRONG PLACE
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
								//Form to confirm deletion
								if(isset($_POST['delete_request']) && ($answer['answer_id']==$_POST['answer_id']))
								{
									$html=$html . "
									<form class='delete_answer' action='answersheet_process.php' method='post'>
									<input type='hidden' name='delete_answer' />
									<input type='hidden' name='cohort' value='" . $cohort . "'/>	
									<input type='hidden' name='answer_id' value='" . $answer['answer_id'] . "'/>
									<input type='submit' class='delete_button' value='Confirm Deletion' />
									</form></td>
									</tr></tbody></table>";									
								}
								//Form to request deletion 
								else{
								$html=$html . "
									<form class='delete_request' action='answersheet_process.php' method='post'>
									<input type='hidden' name='delete_request' />
									<input type='hidden' name='cohort' value='" . $cohort . "'/>	
									<input type='hidden' name='answer_id' value='" . $answer['answer_id'] . "'/>
									<input type='submit' class='delete_request_button' value='Delete' />
									</form></td>
									</tr></tbody></table>";
								}
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
							<input type='hidden' name='create_answer' />
							<input type='hidden' name='feedback_author' value='" . $_SESSION['user']['user_id'] . "'/> 
							<input type='hidden' name='cohort' value='" . $_POST['cohort'] . "' />
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

	function addUser()
	{
		$error_messages=array();
		if((strlen($_POST['first_name'])) == 0)
		{
			array_push($error_messages, 'Please enter a first name.');    //was left blank
		}
		if((strlen($_POST['last_name'])) == 0)
		{
			array_push($error_messages, 'Please enter a last name.');    //was left blank
		}
		if((strlen($_POST['cohort'])) == 0)
		{
			array_push($error_messages, 'Please enter a cohort.');    //was left blank
		}
		if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))    //bad e-mail address
		{
			array_push($error_messages, 'Please enter a correct e-mail address.');
		}
		if(!(empty($error_messages))){
			$html="<div id ='error'>";
			foreach($error_messages as $error_message){
				$html.= $error_message . "<br />";
			}
			$html.="</div>";
		}
		else{
			$query="SELECT first_name, last_name, email FROM users WHERE first_name='{$_POST['first_name']}' AND last_name='{$_POST['last_name']}' and email='{$_POST['email']}'";
			$check=$this->connection->fetch_all($query);

			if(count($check)>0)
			{
				$html="<div id='error'>This user's name and e-mail are already registered.</div>";
			}
			else{
				$password='password';
				if($_POST['cohort']==1)
				{
					$user_level=2;
				}
				else{
					$user_level=1;
				}
				$query="INSERT INTO users (first_name, last_name, email, password, user_level, cohort_id, created_at) VALUES ('{$_POST['first_name']}', '{$_POST['last_name']}', '{$_POST['email']}', '".md5('coding88' . $password . 'y15u01vb0')."', " . $user_level . ", '{$_POST['cohort']}', NOW())";
				mysql_query($query);
				$query="SELECT first_name, last_name, email, cohort_id, cohorts.location AS location, cohorts.start_date FROM users LEFT JOIN cohorts on users.cohort_id=cohorts.id WHERE first_name='{$_POST['first_name']}' AND last_name='{$_POST['last_name']}' and email='{$_POST['email']}'";
				$validation=$this->connection->fetch_all($query);
				foreach($validation as $validate){
					$html="<div id='success'>New user, " . $validate['first_name'] . " " . $validate['last_name'] . " was added to the " . $validate['location'] . " " . $validate['start_date'] . " cohort.  Password was initialized as " . $password . ".</div>";
				}
			}
			
		}
		echo json_encode($html);
	}

	function searchUser()
	{
		$first_name_check=$_POST['name'];
		$last_name_check=$_POST['name'];
		$messy_condition="(first_name LIKE '" . $first_name_check . "%' OR last_name LIKE '" . $last_name_check . "%')";
		$condition=str_replace('"', "", $messy_condition);
		$this->editUserTable($condition);
	}

	function searchByCohort()
	{
		$cohort_check=$_POST['cohort'];
		$condition="cohort_id=" . $cohort_check;
		$this->editUserTable($condition);
	}

	function editUserTable($param)
	{
		if(isset($_POST['delete_user_request']) OR (isset($_POST['edit_user'])) OR (isset($_POST['delete_user'])))
		{
			$param=$_POST['condition'];
		}
		$query = "SELECT users.id AS user_id, first_name, last_name, email, cohort_id, cohorts.location, cohorts.start_date FROM users LEFT JOIN cohorts on cohorts.id=users.cohort_id WHERE " . $param . " ORDER BY last_name ASC";
		$users = $this->connection->fetch_all($query);
		$html = "<table id='user_table' border='1'>
			<thead>
				<tr>
					<th>First Name</th>
					<th>Last Name</th>
					<th>E-mail Address</th>
					<th>Cohort</th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
		";
		foreach($users as $user)
		{
			if((isset($_POST['edit_user']))&&($_POST['user_id']==$user['user_id']))
			{
				$html .= "
					<tr>
						<form class='update_user' action='answersheet_process.php' method='post'>
							<input type='hidden' name='update_user' />";
				$html .='	<input type="hidden" name="condition" value="' . $param . '" />';
				$html .="	<input type='hidden' name='user_id' value='" . $user['user_id'] . "' />
							<td class='min_width'><input type='text' name='first_name' value='" . $user['first_name'] . "'></td>
							<td class='min_width'><input type='text' name='last_name' value='" . $user['last_name'] . "'></td>
							<td><input type='text' name='email' value='" . $user['email'] . "'></td>";
				$query="SELECT cohorts.id as cohort_id, location, start_date from cohorts ORDER BY cohorts.location, cohorts.start_date ASC";
				$results = $this->connection->fetch_all($query);
				$html .= "<td class='min_width'><select name = 'cohort_id'>";
				foreach($results as $cohort)
				{
					if($_POST['cohort_id']==$cohort['cohort_id'])
					{
						$html=$html . "<option value='" . $cohort['cohort_id'] . "' selected>" . $cohort['location'] . " - " . $cohort['start_date'] . "</option>";							
					}
					else
					{
						$html=$html . "<option value=" . $cohort['cohort_id'] . ">" . $cohort['location'] . " - " . $cohort['start_date'] . "</option>";					
					}
				}
// PRESET ON EXISTING VALUE
				$html .= "</select>
						<td><input type='submit' class='update_user_button' value='Update' /></td>
					</form>";
			}
			else
			{
				$html .= "
					<tr>
						<td class='min_width'>{$user['first_name']}</td>
						<td class='min_width'>{$user['last_name']}</td>
						<td>{$user['email']}</td>";
				if($user['cohort_id']==1)
				{
					$html .= "<td class='min_width'>Instructor</td>";
				}
				else
				{
					$html .= "<td class='mid_width'>{$user['location']} - {$user['start_date']}</td>";
				}				
				$html .="
						<td>
							<form class='edit_user' action='answersheet_process.php' method='post'>
								<input type='hidden' name='edit_user' />";
				$html .='		<input type="hidden" name="condition" value="' . $param . '" />';
				$html .="		<input type='hidden' name='cohort_id' value='" . $user['cohort_id'] . "' />
								<input type='hidden' name='user_id' value='" . $user['user_id'] . "' />
								<input type='submit' class='edit_user_button' value='Edit' />
							</form>
						</td>";
			}
			//Form to confirm deletion of user
			if(isset($_POST['delete_user_request']) && ($user['user_id']==$_POST['user_id']))
			{
				$html .="
						<td>
							<form class='delete_user' action='answersheet_process.php' method='post'>
								<input type='hidden' name='delete_user' />";
				$html .='		<input type="hidden" name="condition" value="' . $param . '" />';
				$html .="		<input type='hidden' name='cohort_id' value='" . $user['cohort_id'] . "' />
								<input type='hidden' name='user_id' value='" . $user['user_id'] . "' />
								<input type='submit' class='delete_user_button' value='Confirm Deletion' />
							</form>
						</td></tr>";	
			}
			//Form to request deletion of user 
			else
			{
				$html .="
						<td>
							<form class='delete_user_request' action='answersheet_process.php' method='post'>
								<input type='hidden' name='delete_user_request' />";
				$html .='		<input type="hidden" name="condition" value="' . $param . '" />';
				$html .="		<input type='hidden' name='cohort_id' value='" . $user['cohort_id'] . "' />
								<input type='hidden' name='user_id' value='" . $user['user_id'] . "' />
								<input type='submit' class='delete_user_request_button' value='Delete' />
							</form>
						</td></tr>";	
			}
		}
		$html .= "
				</tbody>
			</table>";

		$data['html'] = $html;
		echo json_encode($data);
		// echo json_encode($html);
	}


	function createCohort()
	{
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
		$html="New cohort created for " . $_POST['location'] . ", starting " . $_POST['start_date'] . ".";
		echo json_encode($html);
	}


	function scheduleDisplay()
	{
		$cohort=$_POST['cohort'];
		$weeks_days_themes=array();
		$answers=array();
		$html="";
		$query="SELECT schedules.id AS schedule_id, week, day, day_theme FROM schedules WHERE schedules.cohort_id={$_POST['cohort']} ORDER BY week, day ASC";
		$weeks_days_themes = $this->connection->fetch_all($query);

		$week=0;
		$day=0;	
		foreach($weeks_days_themes as $week_day_theme)
		{
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
				$html=$html . "</div><!--end of day " . $day . "-->";
			}
		}
		echo json_encode($html);
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

if(isset($_POST['delete_request']))
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

if(isset($_POST['display_cohort']))
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
	// $query="UPDATE answers SET feedback_title='{$_POST['feedback_title']}', feedback_author='{$_POST['feedback_author']}', recipient='{$_POST['recipient']}', available_on='{$_POST['available_on']}', feedback_type='{$_POST['feedback_type']}', url='{$_POST['url']}' WHERE answers.id='{$_POST['answer_id']}'";
	// $display->editAnswers();
	// unset($_POST);
var_dump($_POST);
	unset($_POST);
}




$edit = new Edit;


if(isset($_POST['add_user']))
{
	$edit->addUser();
	unset($_POST);
}


if(isset($_POST['add_cohort']))
{
	$edit->createCohort();
	unset($_POST);
}

if(isset($_POST['search_by_cohort']))
{
	$edit->searchByCohort();
	unset($_POST);
}

if(isset($_POST['search_users']))
{
	$edit->searchUser();
	unset($_POST);
}

if(isset($_POST['edit_user']))
{
	$condition=$_POST['condition'];
	$edit->editUserTable($condition);
	unset($_POST);
}

if(isset($_POST['update_user']))
{
	if($_POST['cohort_id']==1)
	{
		$user_level=2;
	}
	else
	{
		$user_level=1;
	}
	$query="UPDATE users SET first_name='{$_POST['first_name']}', last_name='{$_POST['last_name']}', email='{$_POST['email']}', cohort_id='{$_POST['cohort_id']}', modified_at=NOW(), user_level=" . $user_level . " WHERE users.id='{$_POST['user_id']}'";
	mysql_query($query);
	$condition=$_POST['condition'];
	$edit->editUserTable($condition);
	unset($_POST);
}


if(isset($_POST['delete_user_request']))
{
	$condition=$_POST['condition'];
	$edit->editUserTable($condition);
	unset($_POST);
}

if(isset($_POST['delete_user']))
{

	$query="DELETE FROM users WHERE users.id={$_POST['user_id']}";
	mysql_query($query);
	$condition=$_POST['condition'];
	$edit->editUserTable($condition);
	unset($_POST);	
}


if(isset($_POST['select_cohort_schedule']))
{
	$edit->scheduleDisplay();
	unset($_POST);
}



?>