<?php 
	include_once ('../../vendor/autoload.php');
	if (session_status() == PHP_SESSION_NONE) {
	    session_start();
	}
	//if not logged in redirect him
	if (!isset($_SESSION['user_roll'])) {
		header("Location: ../home/login.php");
	}
	//reference the Dompdf namespace
	use Dompdf\Dompdf;

	$db = new App\marks\Marks();
	$db_student = new App\student\Student();
	$settings = new App\settings\Settings();

	//get user role from session
	$user_role = $_SESSION['user_roll'];
	$operation = 'tabulation-print';

  $semester_map = array(
    '1-1' =>  '1st Year 1st Semester',
    '1-2' =>  '1st Year 2nd Semester',
    '2-1' =>  '2nd Year 1st Semester',
    '2-2' =>  '2nd Year 2nd Semester',
    '3-1' =>  '3rd Year 1st Semester',
    '3-2' =>  '3rd Year 2nd Semester'
    );

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}

	if (!empty($_POST['session']) && !empty($_POST['semester']) ) {
		
		$students = $db_student->assign(array('session' => $_POST['session']))->all_student_session_full();
		$results = $db->assign(array('session' => $_POST['session'], 'semester' => $_POST['semester']))->all_by_session_semester();

//echo '<pre>';
//print_r($students);
// echo sizeof($results);

$content = '<!DOCTYPE html>
			<html>
			<head>
				<title>Page title</title>
				<style type="text/css">
					table{
					   border: 1px solid black;
					   border-collapse: collapse;
					}

					table, th, td {
					    border: 1px solid black;
					}

					td {
						text-align: center;
					}
				</style>
			</head>
			<body>';

$content .= '<div style="text-align: center;">
				<div>
					Result Sheet <br>
					Session: '.$_POST['session'].'

				</div>
				<div>'.$semester_map[$_POST['semester']].'<br></div>
			</div>';


$content .= '<table> 
		<tr>
			<th style="text-align: center;">Roll</th>
			<th style="text-align: center;">Student Name</th>';

for ($i=0; $i < sizeof($results); $i++) { 
	$content .= '<th style="text-align:center;">'. ucwords($results[$i]['course_name']).'<br> 
				<span style="font-size:12px;">( '.$results[$i]['course_code'].' )</span></th>';
}
$content .= '<th style="text-align: center;">Remarks</th>';
$content .= '</tr>';

$content .= '';
foreach ($students as $student) {
	$content .= '<tr>';

	$content .= '<td>'. $student['roll'] .' </td>';
	$content .= '<td>'. $student['student_name'] .' </td>';
	$pass = true;
// print_r($results);
// die();

	$failed_subs = '';

	foreach ($results as $all_results) {
		$result = unserialize($all_results['result']);
		$grade = get_result(unserialize($all_results['result']), $student['student_id']);
		
		if(!$grade) {
			$content .= '<td>N/A</td>';
			$pass = false;
		}else{
			$content .= '<td>'. $grade .'</td>';

			if($grade == 'F') {
				$pass = false;

				if(empty($failed_subs))
					$coma = '';
				else
					$coma = ', ';

				$failed_subs .= $coma.''. $all_results['course_code'];
			}
		}
	}

	if(!$pass) {
		$content .= '<td>Failed<br> <span style="font-size: 12px;">'.$failed_subs.'</span></td>';
	}else{
		$content .= '<td>Pass</td>';
	}

	$content .= '</tr>';
	//echo '<hr>';
}

$content .= '</table>';
$content .= '</body></html>';

//echo $content;

	$dompdf = new Dompdf();
	$dompdf->loadHtml($content);
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->render();
	$dompdf->stream('file.pdf', array( 'Attachment'=>0 ) );

	}

function get_result($results, $student_id){
	$result = false;
	if(isset($results[$student_id])) {
		$result = $results[$student_id];
	}
	return $result;
}	

?>