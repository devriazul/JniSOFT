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


	if (!empty($_POST['student_id'])) {
		if ($db_student->assign(array('student_id' => $_POST['student_id']))->check() ) {
			$student = $db_student->assign(array('student_id' => $_POST['student_id']))->single();
			$profile = $db_student->assign(array('student_id' => $student['student_id']))->single_profile();

			//echo generate_html($student, $profile, $_POST['year']);
			//die();
			// instantiate and use the dompdf class
			$dompdf = new Dompdf();
			$dompdf->loadHtml(generate_html($student, $profile, $_POST['year']));

			// (Optional) Setup the paper size and orientation
			$dompdf->setPaper('A4');

			// Render the HTML as PDF
			$dompdf->render();

			// Output the generated PDF to Browser
			$dompdf->stream('file.pdf', array( 'Attachment'=>0 ) );
		}
	}



?>

<?php 
	function generate_html($student, $profile, $year)
	{
		//configuration
	$student_id = $student['student_id'];
	//$year = 1;
	//$session = '2015-16';
	$semester1 = $year.'-1';
	$semester2 = $year.'-2';

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

	$year_text = 'N/A';
	if($year == 1) {
		$year_text = '1st Year';
	}else if($year == 2) {
		$year_text = '2nd Year';
	}else if($year == 3) {
		$year_text = '3rd Year';
	}else if($year == 4) {
		$year_text = '4th Year';
	}


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
			/*border: 1px solid black;*/
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
			padding: 1px;
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
				'.$year_text.' Examination <br>
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
		</div> <!-- /info -->
		<div class="table">
			<table>
				<tr>
					<th class="th-center">Year & Semester</th>
					<th class="th-center">Course</th>
					<th>Credit Earned</th>
					<th>Letter Grade</th>
					<th>Point</th>
					<th>Semester Grade</th>
					<th>Year Grade /GPA</th>
				</tr>';

	
	$db = new App\marks\Marks();
	$db_student = new App\student\Student();
	$settings = new App\settings\Settings();
	$db_course = new App\course\Course();

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



	// start semester one
	$result1s = $db->assign(array('session' => $session, 'semester' => $semester1))->all_by_session_semester();
	// start semester one
	$db2 = new App\marks\Marks();
	$result2s = $db2->assign(array('session' => $session, 'semester' => $semester2))->all_by_session_semester();

//$settings->log($result2s);
//die();

	$total_point1 = 0;
    $total_credit1 = 0;
    $rowspan = 0;
    $flag = 0;

	$total_point2 = 0;
    $total_credit2 = 0;
    $rowspan2 = 0;
    $flag2 = 0;
	$final_pass_flag = true; //check if fail in any subject 

    if (is_array($result1s)) {
    	//pre calculate total grade
	    foreach ($result1s as $result) {
	    	$my_result = unserialize($result['result']);
	    	if (array_key_exists($student_id, $my_result) && !empty($my_result[$student_id]) ) {
	            $point_value =  $result['credit']*$grade_map[$my_result[$student_id]];
	            $total_point1 += $point_value;
	            $total_credit1 += $result['credit'];
	            //$rowspan++;
	          }
	    }
    }

    if (is_array($result2s)) {
    	//pre calculate total grade semester 2
	    foreach ($result2s as $result) {
	    	$my_result = unserialize($result['result']);
	    	if (array_key_exists($student_id, $my_result) && !empty($my_result[$student_id]) ) {
	            $point_value =  $result['credit']*$grade_map[$my_result[$student_id]];
	            $total_point2 += $point_value;
	            $total_credit2 += $result['credit'];
	            //$rowspan2++;
				if($point_value == 0) {
					$final_pass_flag = false;
				}
	          }
	    }
    }
    
    //extra one space for total row
    $rowspan = $db_course->assign(array('session' => $session, 'semester' => $semester1))->total_by_session_semester();
    $rowspan2 = $db_course->assign(array('session' => $session, 'semester' => $semester2))->total_by_session_semester();

    //echo $semester1;
   // die();
    if ($rowspan != 0) {
    	$rowspan++;
    }
    if ($rowspan2 != 0) {
    	$rowspan2++;
    }

    $grade_point1 = $total_credit1>0?round($total_point1/$total_credit1, 2): 0;
    $grade_point2 = $total_credit2>0? round($total_point2/$total_credit2, 2): 0;
    $final_grade = $total_credit2>0?round( ($total_point1 + $total_point2) / ($total_credit1 + $total_credit2),2):0;

     //draw table for semester 1
    if(is_array($result1s))
    foreach ($result1s as $result) {
      //grab my result
      $my_result = unserialize($result['result']);

      if (array_key_exists($student_id, $my_result) && !empty($my_result[$student_id]) ) {
        $grade_value =  $my_result[$student_id];
      }else{
        $grade_value = 'N/A';
      }

      if (array_key_exists($student_id, $my_result) && !empty($my_result[$student_id]) ) {
        $point_value =  $result['credit']*$grade_map[$my_result[$student_id]];
      }else{
        $point_value = 'N/A';
      }

   $html .= '<tr>
    		'.($flag==0?"<td rowspan=\"$rowspan\">
					Year $year <br>
					Semester 1 <br>
					<p style=\"text-align: left;\">Month: ".date('F', strtotime($result['created_at']))."<br> 
					Year: ".date('Y', strtotime($result['created_at']))."</p>
				</td>":"").'

            <td class="th-left">'.$result['course_name'].'</td>
            <td>'.$result['credit'].'</td>
            <td>'.$grade_value.'</td>
            <td>'.$point_value.'</td>
            '.($flag==0?"<td rowspan=$rowspan> $grade_point1 </td>":"").'
            '.($flag==0?"<td rowspan=".($rowspan+$rowspan2)."> $final_grade </td>":"").'
          </tr>';
          $flag = 1;

    } //end foreach

    //if ($total_credit1 != 0) {
    $html .= '<tr>
            <td><b>Total</b></td>
            <td><b>'.$total_credit1.'</b></td>
            <td>&nbsp;</td>
            <td><b>'.$total_point1.'</b></td>
          </tr>';
    //}
    //end semester one



    //$ts = array('1-1', '1-2');



    /******* SEMESTER 2 ******/
    //semester 2
    //draw table for semester 1
    if(is_array($result2s))
    foreach ($result2s as $result) {
      //grab my result
      $my_result = unserialize($result['result']);

      if (array_key_exists($student_id, $my_result) && !empty($my_result[$student_id]) ) {
        $grade_value =  $my_result[$student_id];
      }else{
        $grade_value = 'N/A';
      }

      if (array_key_exists($student_id, $my_result) && !empty($my_result[$student_id]) ) {
        $point_value =  $result['credit']*$grade_map[$my_result[$student_id]];
		if($point_value == 0) {
        	$final_pass_flag = false;
        }
      }else{
        $point_value = 'N/A';
      }

    $html .= '<tr>
    		'.($flag2==0?"<td rowspan=\"$rowspan2\">
					Year $year <br>
					Semester 2 <br>
					<p style=\"text-align: left;\">Month: ".date('F', strtotime($result['created_at']))."<br> 
					Year: ".date('Y', strtotime($result['created_at']))."</p>
				</td>":"").'

            <td class="th-left">'.$result['course_name'].'</td>
            <td>'.$result['credit'].'</td>
            <td>'.$grade_value.'</td>
            <td>'.$point_value.'</td>
            '.($flag2==0?"<td rowspan=$rowspan2> $grade_point2 </td>":"").'
          </tr>';
          $flag2 = 1;

    } //end foreach

    //if ($total_credit2 != 0) {
   	$html .= '<tr>
            <td><b>Total</b></td>
            <td><b>'.$total_credit2.'</b></td>
            <td>&nbsp;</td>
            <td><b>'.$total_point2.'</b></td>
          </tr>';
     // }
    //end semester two


	//echo '<pre>';
	//print_r($result1s);
	//echo '</pre>';
      $html .= '</table>
		</div>';

	if ($total_credit1 > 0) {
			$letter_grade = $grade_map_r[floor(($total_point1+$total_point2)/($total_credit1+$total_credit2))];
			if ($letter_grade == 'F' || !$final_pass_flag) {
				$letter_grade = 'FAILED';
			}else{
				$letter_grade = 'PASSED';
			}

				
		$html .= '<div class="summery">
					<span>Total Credit Earned: '.($total_credit1+$total_credit2).'</span>
					<span>Semester Grade: '.round(($total_point1+$total_point2)/($total_credit1+$total_credit2), 2).'</span>
					<span>Result: '.$letter_grade.'</span>
				</div>';
	}

		//footer
		$html .= '<div class="footer">
			<span>Prepared by <br> Date:</span>
			<span>Checked & Verified by: <br> Date:</span>
			<span>Principal/Nursing Instructor In charge</span>
		</div>
		<div class="footnote">* Not valid without seal</div>
	</div>
</body>
</html>';
	return $html;
	}

?>







