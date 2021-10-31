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


	if (!empty($_POST['student_id']) && !empty($_POST['semester']) && !empty($_POST['term']) && !empty($_POST['date']) ) {
		if ($db_student->assign(array('student_id' => $_POST['student_id']))->check() ) {
			$student = $db_student->assign(array('student_id' => $_POST['student_id']))->single();
			$profile = $db_student->assign(array('student_id' => $student['student_id']))->single_profile();


			$data = array(
				'semester' => $_POST['semester'],
				'term' => $_POST['term'],
				'date' => $_POST['date'],
				);

			//echo html($student, $profile, $data);
			///die();
			// instantiate and use the dompdf class
			$dompdf = new Dompdf();
			$dompdf->loadHtml(html($student, $profile, $data));
			$dompdf->setPaper('A4');
			$dompdf->render();
			$dompdf->stream('file.pdf', array( 'Attachment'=>0 ) );

		}
	}else{
		header("Location: ../student/index.php");
	}


	function html($student, $profile, array $data)
	{
		$db_db = new App\db\Db();
		$institute = $db_db->assign(array('key' => 'institute'))->get_value();
		$institute  = is_array($institute)?$institute['_value']:'...............................................';

		$student_name = !empty($student['student_name'])?$student['student_name']: 'N/A';
		$father_name = !empty($profile['father_name'])?$profile['father_name']: 'N/A';
		$mother_name = !empty($profile['mother_name'])?$profile['mother_name']: 'N/A';
		$roll = !empty($student['roll'])?$student['roll']: 'N/A';

		$semester = $data['semester'];
		$term = $data['term'];
		$date = $data['date'];


		$html = '<!DOCTYPE HTML>
					<html>
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
						<div class="container">
							<div class="box-1">
								<div class="title">
									<h1>'.ucwords($institute).'</h1>
									<h4>Mirpur, Dhaka-1216</h4>
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
									<span class="left">
										<span class="upper-line">----------------------</span><br>
										Student Signature
									</span>
									<span class="right">
										<span class="upper-line">------------------------</span><br>
										Authorize Signature
									</span>
								</div>
							</div>
							<div class="cut">
								&nbsp;
							</div>
							<div class="box-2">
								
								<div class="title">
									<h1>'.ucwords($institute).'</h1>
									<h4>Mirpur, Dhaka-1216</h4>
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
								<div class="notice">
									Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eveniet assumenda, repudiandae recusandae quis quae, suscipit quia nostrum pariatur error. Nulla cupiditate, amet sunt sint quis enim quaerat maxime et suscipit.
								</div>
								<div class="footer">
									<div class="bottom-right">
										<span class="upper-line">------------------------</span><br>
										Authorize Signature
									</div>
								</div>
							
							</div>
						</div>
					</body>
					</html>';

	
	return $html;

	}



?>