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
	$operation = 'marks-print';

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}


	if (!empty($_POST['session']) && !empty($_POST['semester']) && !empty($_POST['student_id'])) {
		if ($db_student->assign(array('student_id' => $_POST['student_id']))->check() ) {
			$student = $db_student->assign(array('student_id' => $_POST['student_id']))->single();
			$profile = $db_student->assign(array('student_id' => $student['student_id']))->single_profile();
			$results = $db->assign(array('session' => $student['session'], 'semester' => $_POST['semester']))->all_by_session_semester();
//$settings->log($results);
//die();
			if (is_array($results) && !empty($results)) {
				//echo html_for_single_semester($student, $profile, $results);
				//die();
				// instantiate and use the dompdf class
				$dompdf = new Dompdf();
				//$table = '';
				
				
				$final_html = html_for_single_semester($student, $profile, $results, $_POST['semester']);
    			$final_html = preg_replace('/>\s+</', '><', $final_html);
    			//echo $final_html;
    			//die();
    			$dompdf->loadHtml($final_html);
				
				// (Optional) Setup the paper size and orientation
				$dompdf->setPaper('A4');

				// Render the HTML as PDF
				$dompdf->render();

				// Output the generated PDF to Browser
				$dompdf->stream('file.pdf', array( 'Attachment'=>0 ) );

			} //result is not empty
		} //valid student
	}

	if (!empty($_POST['session']) && !empty($_POST['semester']) && !empty($_POST['student_id']) && !empty($_POST['year'])) {
		
	}




function html_for_single_semester($student, $profile, $results, $semester) {
	$student_name = !empty($student['student_name'])?$student['student_name']: 'N/A';
	$father_name = !empty($profile['father_name'])?$profile['father_name']: 'N/A';
	$mother_name = !empty($profile['mother_name'])?$profile['mother_name']: 'N/A';
	$roll = !empty($student['roll'])?$student['roll']: 'N/A';
	$bnc_roll = !empty($student['bnc_roll'])?$student['bnc_roll']: 'N/A';
	$session = !empty($student['session'])?$student['session']: 'N/A';
	$date_of_birth = !empty($profile['date_of_birth'])? date('d F Y', strtotime($profile['date_of_birth'])): 'N/A';
	
	$db_db = new App\db\Db();
	$institute = $db_db->assign(array('key' => 'institute'))->get_value();
	$code = $db_db->assign(array('key' => 'college_code'))->get_value();
	$institute = is_array($institute)?$institute['_value']:'NURSING INSTITUTE ..............................';
	$code = is_array($code)?$code['_value']:'...........';


	  //result map
	  $grade_map = array(
	    'A' => 4, 
	    'B' => 3, 
	    'C' => 2, 
	    'D' => 1, 
	    'F' => 0 
	    );
	  $grade_map_r = array(
	    '4' => 'A', 
	    '3' => 'B', 
	    '2' => 'C', 
	    '1' => 'D', 
	    '0' => 'F' 
	    );

	  //semister map
	  $semester_map = array(
	    '1-1' => '1st Year 1st Semester',
	    '1-2' => '1st Year 2nd Semester',
	    '2-1' => '2nd Year 1st Semester',
	    '2-2' => '2nd Year 2nd Semester',
	    '3-1' => '3rd Year 1st Semester',
	    '3-2' => '3rd Year 2nd Semester'
	    );

	$html = '<!DOCTYPE html>
			<html>
			<head>
				<title>Page title</title>
				<style type="text/css">

					.container {
						margin-left: 10px; 	
						margin-right: 10px; 	
						margin-top: 5px; 	
						font-size: 14px;
					}
					.top h3 {
						text-align: center;
					}
					.header {
						width: 100%;
						//min-height: 220px;
						clear: both;
						line-height:1.5;
					}
					.box-left {
						float: left;
						width: 30%;
					}
					.box-middle {
						float: left;
						width: 40%;
						text-align: center;
					}
					.box-right {
						float: left;
						width: 30%;
					}
					.mark-list {
						border: 1px solid black;
						padding: 2px;
						width: 100%;
						font-size: 12px;
						margin-bottom: 20px;
					}

					.monogram {
					/*	border: 1px solid black;*/
						width: 80px;
						height: 80px;
					}

					.monogram img {
						width: 80px;
						height: 80px;
					}

					.info {
						width: 100%;
						clear: both;						
						line-height:1.5;
					}
					.info-left {
						float: left;
						width: 50%;
					}
					.info-right {
						float: left;
						width: 50%;
					}
					.info-single {
						clear: both;
						margin-bottom: 25px;
					}
					.info-single span{
						width: 33%;
						float: left;
					}
					table, th, td {
					   border: 1px solid black;
					}
					.table{

					}

					.table table {
						width: 100%;
						border-collapse: collapse;
						text-align: center;
					}

					.table th,td {
						padding: 5px 1px;
					}

					.th-left {
						text-align: left;
					}
					.summery {
						margin-top: 10px;
						text-align: right;
					}
					.summery span {
						padding-left: 10px;
					}

					.footer {
						margin-top: 80px;
					}

					.footer span {
						float: left;
						width: 33%;
					}

					.footnote {
						width: 100%;
						clear: both;
						padding-top: 25px;
					}
				</style>
			</head>
			<body>
				<div class="container">
				<br><br><br><br>
					<div class="top">
						<h3><u>Transcript of Academic Records</u></h3>
					</div>
					<div class="header">
						<div class="box-left">
							<div class="monogram">
								<img src="../../assets/images/logo.png">
							</div>
						</div>
						<div class="box-middle">
							<br>
							<span>'.$institute.'</span>
							<br><span>College Code: '.$code.'</span><br><br>
							Transcript of Academic Records
						</div>
						<div class="box-right">
							Book No ............... <br>
							Serial No ................ <br>
							Date: '.date('d F Y').' <br><br>
							<div class="mark-list">
								A = 4 (80.00 - 100%) <br>
								B = 3 (70.00 - 79.99%) <br>
								C = 2 (60.00 - 69.99%) <br>
								D = 1 (50.00 - 59.99%) <br>
								F = Fail ( &#60; 49.99%) <br>
							</div>
						</div>
					</div> <!-- /header -->
					<div class="info">
						<div class="info-left">
							Name of Student: '.$student_name.' <br>
							Father\'s Name: '.$father_name.' <br>
							'. $semester_map[$semester].' <br>
						</div>
						<div class="info-right">
							Date of Birth: '.$date_of_birth.'<br>
							Mother\'s Name: '.$mother_name.' <br>
						</div>
						<div class="info-single">
							<span>Roll No: '.$roll.'</span>
							<span>BNMC Reg No: '.$bnc_roll.'</span>
							<span>Session: '.$session.'</span>
						</div>
					</div> <!-- /info -->';


		//table
	$html .= '<div class="table">
				<table>
					<tr>
						<th class="th-left">Course</th>
						<th>Credit Earned</th>
						<th>Letter Grade</th>
						<th>Point</th>
						<th>Semester Grade</th>
					</tr>';


		$total_point = 0;
        $total_credit = 0;
        $rowspan = 0;
        $flag = 0;

        //calculate total grade
        foreach ($results as $result) {
        	$my_result = unserialize($result['result']);
        	if (array_key_exists($student['student_id'], $my_result) && !empty($my_result[$student['student_id']]) ) {
	            $point_value =  $result['credit']*$grade_map[$my_result[$student['student_id']]];
	            $total_point += $point_value;
	            $total_credit += $result['credit'];
	            //$rowspan++;
	          }
        }
        //extra one space for total row
        $db_course = new App\course\Course();
        $rowspan = $db_course->assign(array('session' => $session, 'semester' => $result['semester']))->total_by_session_semester();
        if ($rowspan != 0) {
        	$rowspan++;
        }

        if ($total_credit >0) {
        	$grade_point = round($total_point/$total_credit, 2);
        }else{
        	$grade_point = 0;
        }

        //draw table for this semester
        $total_point = 0;
        $total_credit = 0;
        foreach ($results as $result) {
          //grab my result
          $my_result = unserialize($result['result']);

          if (array_key_exists($student['student_id'], $my_result) && !empty($my_result[$student['student_id']]) ) {
            $grade_value =  $my_result[$student['student_id']];
          }else{
            $grade_value = 'N/A';
          }

          if (array_key_exists($student['student_id'], $my_result) && !empty($my_result[$student['student_id']]) ) {
            $point_value =  $result['credit']*$grade_map[$my_result[$student['student_id']]];
            $total_point += $point_value;
            $total_credit += $result['credit'];
          }else{
            $point_value = 'N/A';
          }

        $html .= '<tr>
                <td class="th-left">'.$result['course_name'].'</td>
                <td>'.$result['credit'].'</td>
                <td>'.$grade_value.'</td>
                <td>'.$point_value.'</td>
                '.($flag==0?"<td rowspan=$rowspan> $grade_point </td>":"").'
              </tr>';
              $flag = 1;

        } //end foreach

        //if ($total_credit != 0) {
        $html .= '<tr>
                <td><b>Total</b></td>
                <td><b>'.$total_credit.'</b></td>
                <td>&nbsp;</td>
                <td><b>'.$total_point.'</b></td>
              </tr>';
        //}


       $html .= '</table>
			</div> <!-- /info table -->';

	if ($total_credit > 0) {
		$letter_grade = $grade_map_r[round($total_point/$total_credit)];
	}else{
		$letter_grade = 'N/A';
	}

	if ($letter_grade == 'F') {
		$letter_grade = 'FAIL';
	}else{
		$letter_grade = 'PASS';
	}



	//footer
	$html .= '<div class="summery">
				<span>Total Credit earned: '.$total_credit.'</span>
				<span>Semester Grade: '.($total_credit>0 ? round($total_point/$total_credit, 2) : 'N/A').'</span>
				<span>Result: '.$letter_grade.'</span>
			</div>
			<div class="footer">
				<span>Prepared by <br> Date:</span>
				<span>Checked & Verified by: <br> Date:</span>
				<span>Principal/Nursing Instructor In charge</span>
			</div>
			<br/><br/><br/><br/>
		    <br/><br/>
			<div class="footnote">* Not valid without seal</div>
		</div>
	</body>
	</html>';	

	return $html;	
}
?>