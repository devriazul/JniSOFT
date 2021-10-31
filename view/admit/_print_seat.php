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
	$operation = 'seat-plan-print';

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}


	if (!empty($_POST['selected_students']) && !empty($_POST['session'])) {
		$selected_students = $_POST['selected_students'];
		$students = $db_student->assign(array('session' => $_POST['session']))->all_student_session_full();

		$content = head();
		$flag = true;
		$content .= '<table>';

		foreach ($students as $student) {
			if(!in_array($student['student_id'], $selected_students )) {
				continue;
			}

			if ( $flag ) {
				$content .= '<tr>';
				$profile = $db_student->assign(array('student_id' => $student['student_id']))->single_profile();

				$data = array(
					'semester' => $_POST['semester'],
					'term' => $_POST['term'],
					'session'	=> $_POST['session'],
					);

				$content .= html($student, $profile, $data);
				$flag = false;
			}else{
				$profile = $db_student->assign(array('student_id' => $student['student_id']))->single_profile();

				$data = array(
					'semester' => $_POST['semester'],
					'term' => $_POST['term'],
					'session'	=> $_POST['session'],
					);

				$content .= html($student, $profile, $data);
				$flag = true;
				$content .= '</tr>';
			}
		}

			$content .= '</table>';

			$content .= leg();
			//echo $content;

			//die();
			$dompdf = new Dompdf();
			$final_html = preg_replace('/>\s+</', '><', $content);
			$dompdf->loadHtml($final_html);
			$dompdf->setPaper('A4');
			$dompdf->render();
			$dompdf->stream('file.pdf', array( 'Attachment'=>0 ) );
		
	}else{
		header("Location: ../admit/index.php");
	}




	function html($student, $profile, array $data)
	{
		$db_db = new App\db\Db();
		$institute = $db_db->assign(array('key' => 'institute'))->get_value();
		$institute  = is_array($institute)?$institute['_value']:'...............................................';

		$address = $db_db->assign(array('key' => 'college_address'))->get_value();
		$address  = is_array($address)?$address['_value']:'Please Add Address from settings';
  		$subject = $db_db->assign(array('key' => 'college_subject'))->get_value();
  		$subject  = is_array($subject)?$subject['_value']:'Please Add subject from settings';


		$roll = !empty($student['roll'])?$student['roll']: 'N/A';

		$semester = $data['semester'];
		$term = $data['term'];

		$html = '<td>
					<div class="box">
						Session: '.$data['session'].' <br>
						'.$data['semester'].', '.$data['term'].' <br>
						<span class=".institute">'.$institute.' </span><br>
						<span class="name">'.$student['student_name'].'</span> <br>
						<div class="roll">Roll No - '.$student['roll'].' </div>
					</div>
				</td>';
				
	return $html;
	}


	function head()
	{
		$head = '<!DOCTYPE HTML>
					<html lang="en">
					<head>
						<title>hello</title>
						<style type="text/css">
							body{
								padding: 0;
								margin: 0;
							}
							.container{
							}

							.box {
								border: 1px solid black;
								text-align:center;
								padding: 10px;
								font-size: 18px;
							}

							.institute {
								font-size: 15px;
							}

							.roll {
								border: 1px solid black;
								font-size: 22px;
							}

							.name {
								font-size: 20px;
							}

							table {
								width: 100%;
							}

							.clear {
								clear: both;
							}
							.clear:after {
							  content: "";
							  clear: both;
							  display: table;
							}
						</style>
					</head>
					<body>
					<div class="container">';

		return $head;
	}

	function leg()
	{
		return '</div>
					</body>
					</html>';
	}



?>