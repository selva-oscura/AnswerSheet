<?php 
include("connection.php");

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
		$query="SELECT week, day, day_theme, assignment_title, feedback_title, feedback_author, DATE(available_on) AS available_date, users.first_name AS first_name FROM answers LEFT JOIN users ON users.id=answers.feedback_author WHERE (answers.recipient={$_SESSION['user']['id']} OR answers.recipient={$_SESSION['user']['cohort']} ) AND ((answers.available_on >= NOW()-INTERVAL 3 DAY) AND (answers.available_on <= NOW())) ORDER BY answers.available_on DESC";
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
				$html=$html . "<li>Week " . $recent_addition['week'] . ", Day " . $recent_addition['day'] . " - " . $recent_addition['day_theme'] . " - " . $recent_addition['assignment_title'] . " - " . $recent_addition['feedback_title'] . " - posted by " . $recent_addition['first_name'] . " - " . $recent_addition['available_date'] . "</li>";
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
		$query="SELECT week, day, day_theme, assignment_title, feedback_title, feedback_author, DATE(available_on) AS available_date, feedback_type, url, users.first_name AS first_name FROM answers LEFT JOIN users ON users.id=answers.feedback_author WHERE (answers.recipient={$_SESSION['user']['id']} OR answers.recipient={$_SESSION['user']['cohort']} ) AND (answers.available_on <= NOW()) ORDER BY week ASC, day ASC, available_date ASC";
		$answers = $this->connection->fetch_all($query);
		if(!(count($answers)>0))
		{
			$html="<p>No answers have been posted yet.  Please check back soon.</p>";
		}
		else
		{
			for($week=1; $week<10; $week++){
				$html=$html ."<h3 id='week" . $week . "' class='week_bar'><img class='a_right' src='img/arrow_right_white.png' /><img class='a_down' src='img/arrow_down_white.png'/>Week " . $week . "</h3>";
				for($day=1;$day<6; $day++){
					$html=$html ."<div class='week" . $week . "'>
						<p class='day_bar wk" . $week . "'>Day " . $day;
					$day_theme_catcher=0;
					foreach($answers as $answer){
						if($day_theme_catcher==0){						
							if(($answer['week']==$week) && ($answer['day']==$day)){
								$html=$html . " - " . $answer['day_theme'];
								$day_theme_catcher=1;
							}
						}
					}
					$html=$html . "</p>";
					foreach($answers as $answer){
						if(($answer['week']==$week) && ($answer['day']==$day)){
							$html=$html . "<table class='assignment_space'><tbody><tr><td>" . $answer['assignment_title'] . " - " . $answer['feedback_title'] . " from " . $answer['first_name'];
							if($answer['feedback_type']==1){
								$html=$html . ", video, " . $answer['available_date'] ."</td><td class='get_media'><a href='media/" . $answer['url'] . "' class='vid" . $vid . "' target='vid" . $vid . "'>View</td></tr></tbody></table><iframe name='vid" . $vid . "'id='vid" . $vid . "' frameborder='0' allowfullscreen></iframe>";
								$vid++;
							}
							else{
								$html=$html . ", code, " . $answer['available_date'] ."</td><td class='get_media'><a href='media/" . $answer['url'] . "'>Download</a></td></tr></tbody></table>";
							}
						}
					}
					$html=$html . "</div><!--end of day " . $day . "-->";
				}
			}
		
}		echo $html;
	}


	function cohortDropdown()
	{
		
		//NEED TO REVISE DATABASE STRUCTURE TO CREATE A GOOD TABLE FOR COHORT/LOCATION/ETC.
		// $query="SELECT location.id, users.location, users.cohort, location_labels.location_name FROM users LEFT JOIN location ON location_labels.id=users.location ORDER BY users.cohort ASC";
		// $results = fetch_all($query);
		// $html = "<div id='cohort_select'><h2>Select a Cohort:</h2> <form id='display_cohort' action='answersheet_process.php' method='post'><select name = 'cohort'>";

		// foreach($results as $name)
		// {
		// 	$html=$html . "<option value=" . $name['Code'] . ">" . $name['Name'] . "</option>";
		// }
		//UNTIL THEN, THIS DROPDOWN WILL DO

		$html = "<div id='cohort_select'><h3>Select a Cohort:</h3> <form id='display_cohort' action='answersheet_process.php' method='post'><select name = 'cohort'>";
		$html=$html . "<option value=' '> </option><option value='120130212'>Mountain View - 2013.02.12</option>
			<option value='120130408'>Mountain View - 2013.04.08</option>
			<option value='120130520'>Mountain View - 2013.05.20</option>
			<option value='120130624'>Mountain View - 2013.06.24</option>
			<option value='120130729'>Mountain View - 2013.07.29</option>
			<option value='120130902'>Mountain View - 2013.09.02</option>
			<option value='120131007'>Mountain View - 2013.10.07</option>
			<option value='220130812'>Seattle - 2014.02.12</option>";
		$html=$html . "<input type='submit' id='button' value='Display' /></select></form></div>";



		//submit button eliminated after adding the submit on change functionality to the dropdown field
		// $html=$html . "<input type='submit' id='button' value='Show Data' />";

		// $html=$html . "</select></form></div>";
		echo $html;
	}

}






$display = new Display;












// Class Table extends Process
// {
// 	function friendsTable()
// 	{
// 		$users=array();
// 		$query = "SELECT CONCAT (users.first_name, ' ', users.last_name) AS name, users.email AS email FROM users LEFT JOIN friends ON friends.friend_id = users.id WHERE friends.user_id = {$_SESSION['user']['id']}";
// 		$users = $this->connection->fetch_all($query);	
// 		$html = "<table id='friendstable' class='tablesorter'> 
// 				<thead> 
// 					<tr> 
// 					    <th>Name</th> 
// 					    <th>E-mail</th> 
// 					</tr> 
// 				</thead> 
// 				<tbody>";
// 		if(!(count($users))>0)
// 		{
// 			$html=$html . "<tr><td>None</td><td> </td></tr>";
// 		}
// 		else
// 		{
// 			foreach($users as $friend)
// 		{
// 			$html=$html . "<tr><td>" . $friend['name'] . "</td><td>" . $friend['email'] . "</td></tr>";
// 		}

// 		}
// 		$html=$html . "</tbody>
// 			</table>";
// 		echo $html;
// 	}

// 	function usersTable()
// 	{
// 		$users=array();
// 		$query = "SELECT users.id, CONCAT (users.first_name, ' ', users.last_name) AS name, users.email AS email FROM users WHERE users.id <> {$_SESSION['user']['id']}";
// 		$users = $this->connection->fetch_all($query);	
// 		$html = "<table id='userstable' class='tablesorter'> 
// 				<thead> 
// 					<tr> 
// 					    <th>Name</th> 
// 					    <th>E-mail</th>
// 					    <th>Action</th>
// 					</tr> 
// 				</thead> 
// 				<tbody>";
// 		foreach($users as $user)
// 		{
// 			$query="SELECT friends.user_id, friends.friend_id FROM friends WHERE ((friends.user_id={$_SESSION['user']['id']}) && (friends.friend_id={$user['id']}))";
// 			$check=$this->connection->fetch_all($query);
// 			if(count($check)>0)
// 			{
// 				$html=$html . "<tr><td>" . $user['name'] . "</td><td>" . $user['email'] . "</td><td> Friends </td></tr>";
// 			}
// 			else
// 			{
// 				$html=$html . "<tr><td>" . $user['name'] . "</td><td>" . $user['email'] . "</td><td>
// 				<form id = 'friending' action='library.php' method='post'><input type='hidden' name='action' value='select_friend'><input type='hidden' name='user_id' value='" . $_SESSION['user']['id'] . "' /><input type='hidden' name='friend_id' value='" . $user['id'] . "'/><input type='submit' class='submit_button' value='Add as a Friend' /></form>
// 				</td></tr>";	
// 			}
// 		}
// 			$html=$html . "</tbody>
// 		</table>";
// 		echo $html;
// 	}
// }




// Class Friend extends Process
// {
// 	function addFriend()
// 	{
// 		$query="INSERT INTO friends (user_id, friend_id) VALUES ({$_POST['user_id']}, {$_POST['friend_id']})";
// 		mysql_query($query);
// 		$query="INSERT INTO friends (user_id, friend_id) VALUES ({$_POST['friend_id']}, {$_POST['user_id']})";
// 		mysql_query($query);
// 		header("location: main.php");
// 	}
// }

// if(isset($_POST['action']) and $_POST['action'] == "select_friend")
// {
// 	$friend = new Friend;
// 	$friend->addFriend();
// }

?>