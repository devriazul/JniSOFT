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
	$operation = 'admit-print';

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}

// print_r($_POST);
// die();

	if (!empty($_POST['selected_students']) && !empty($_POST['session'])) {
		$selected_students = $_POST['selected_students'];
		$students = $db_student->assign(array('session' => $_POST['session']))->all_student_session_full();

		$content = head();
		foreach ($students as $student) {
			if(!in_array($student['student_id'], $selected_students )) {
				continue;
			}

			if ( true ) {
				//$student = $db_student->assign(array('student_id' => $_POST['student_id']))->single();
				$profile = $db_student->assign(array('student_id' => $student['student_id']))->single_profile();

				$data = array(
					'semester' => $_POST['semester'],
					'term' => $_POST['term'],
					'date' => $_POST['date'],
					);

				$content .= html($student, $profile, $data);
			}
		}

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


		$student_name = !empty($student['student_name'])?$student['student_name']: 'N/A';
		$father_name = !empty($profile['father_name'])?$profile['father_name']: 'N/A';
		$mother_name = !empty($profile['mother_name'])?$profile['mother_name']: 'N/A';
		$roll = !empty($student['roll'])?$student['roll']: 'N/A';

		$semester = $data['semester'];
		$term = $data['term'];
		$date = $data['date'];


		$html = '<div class="box-1">
								<div class="title">
									<h1>'.ucwords($institute).'</h1>
									<h4>'.ucwords($address).'</h4>
									<h2 class="bold">Admit Card</h2>
									<h3 class="under-line"><u>Diploma-In-Nursing Course '.$semester.' '.$term.' Exam-'.(date('Y')).'</u></h3>
								</div>
								<div class="description">
									<table>
										<tr>
											<th>Roll No:</th>
											<td>'.$roll.'</td>
											<th>Student Name:</th>
											<td>'.$student['student_name'].'</td>
										</tr>
										<tr>
											<th>&nbsp;</th>
											<td>&nbsp;</td>
											<th>Father Name:</th>
											<td>'.$father_name.'</td>
										</tr>
										<tr>
											<th>Session:</th>
											<td>'.$student['session'].'</td>
											<th>Mother Name:</th>
											<td>'.$mother_name.'</td>
										</tr>
									</table>
								</div>
								<div class="footer clear">
									<table style="width:100%;">
										<tr>
											<td>
												<span class="upper-line">
													Student Signature
												</span>	
											</td>
											<td style="text-align:right;">
												<span class="upper-line">
													Student Signature
												</span>	
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="cut">
								&nbsp;
							</div>
							<div class="box-2">
								
								<div class="title">
									<h1>'.ucwords($institute).'</h1>
									<h4>'.ucwords($address).'</h4>
									<h2 class="bold">Admit Card</h2>
									<h3 class="under-line"><u>'.$subject.' '.$semester.' '.$term.' Exam-'.(date('Y')).'</u></h3>
								</div>
								<div class="description">
									<table>
										<tr>
											<th>Roll No:</th>
											<td>'.$roll.'</td>
											<th>Student Name:</th>
											<td>'.$student['student_name'].'</td>
										</tr>
										<tr>
											<th>&nbsp;</th>
											<td>&nbsp;</td>
											<th>Father Name:</th>
											<td>'.$father_name.'</td>
										</tr>
										<tr>
											<th>Session:</th>
											<td>'.$student['session'].'</td>
											<th>Mother Name:</th>
											<td>'.$mother_name.'</td>
										</tr>
									</table>
								</div>
								<div class="notice">
									He/She is allowed to appear in the '.$semester.' '.$term.' Exam.Which will be held on the '.$date.'.
									<br> B.N. It is prohibited to use Cell Phone in the exam hall.
								</div>
								<div class="footer">
									<div class="bottom-right">
										<span class="upper-line">
											Authorize Signature
										</span>
									</div>
								</div>
							</div>

							<div class="page-break"></div>';
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

							.box-1{
								width: 100%;
								border: 2px solid black;
								clear: both;
							}
							.cut {
								border-bottom: 2px dashed black;
								margin-bottom: 20px;
								clear: both;
							}
							.box-2{
								width: 100%;
								min-height: 600px;
								border: 2px solid black;
								clear: both;
							}
							.footer {
								clear: both;
								width: 100%;
								margin-top:10px;
							}
							.page-break {
								page-break-after: always;
							}

							.page-break:last-child {
								page-break-after: avoid;
							}

							.title{
								text-align: center;
							}

							.title h1 {
								margin: 0px;
								padding: 0px;
							}

							.title h2 {
								margin-top: 10px;
								margin-bottom: 0px;
								padding: 0px;
							}
							.title h4 {
								margin: 0px;
								padding: 0px;
							}
							.bold {
								background-color: black;
								color: white;
								width: 200px;
								text-align: center;
								margin-left: 36%;
							}
							.under-line {

							}
							.upper-line {
								border-top: 1px dashed black;
								padding-top: 5px;
								font-size: 20px;
							}
							.left {
								float: left;
								width: 50%;
								text-align: left;
								margin-left: 20px;
							}
							.right{
								text-align: right;
								float: left;
								width: 45%;
								padding-bottom: 20px;
							}
							.bottom-right {
								text-align: right;
								margin-right: 20px;
								padding-bottom: 20px;
							}
							.notice{
								padding: 20px;
								font-size: 20px;
							}
							table{
								font-size: 20px;
								padding: 20px;
								clear: both;
							}
							table th{
								padding-right: 20px; 
								text-align: right;
							}
							td {
								padding-right: 20px;
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