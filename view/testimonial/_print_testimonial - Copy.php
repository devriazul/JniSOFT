<?php
include_once ('../../vendor/autoload.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
	//if not logged in redirect him
	if (!isset($_SESSION['user_roll'])) {
		header("Location: ../home/login.php");
	}
	use Dompdf\Dompdf;

	$settings = new App\settings\Settings();

	//get user role from session
	$user_role = $_SESSION['user_roll'];
	$operation = 'testimonial-print';

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}

	if (!isset($_POST['student_id'])) {
		header("Location: ../home/403.php");
	   die();
	}

	$db_student = new App\student\Student();

	if ($db_student->assign(array('student_id' => $_POST['student_id']))->check() ) {
		$student = $db_student->assign(array('student_id' => $_POST['student_id']))->single();
		$profile = $db_student->assign(array('student_id' => $student['student_id']))->single_profile();

		$cgpa = get_cgpa($student, $profile);
		 if ($cgpa) {
		 	//echo 'CGPA: '. $cgpa;
			dom_print($cgpa, $student, $profile);
		 }else{
		 	echo 'Sorry this student is not completed his/her course or has fail in any subject.';
		 }
		//echo print_testimonial($cgpa, $student, $profile );
		//die();

			//dom_print(3.8, $student, $profile);
		}

		function dom_print($cgpa, $student, $profile) {
			// instantiate and use the dompdf class
			$dompdf = new Dompdf();
			$final_html = print_testimonial($cgpa, $student, $profile);
			$final_html = preg_replace('/>\s+</', '><', $final_html);
// 			$final_html = $dompdf->print_testimonial($cgpa, $student, $profile );
			
			$dompdf->loadHtml($final_html);
			// (Optional) Setup the paper size and orientation
			$dompdf->setPaper('A4', 'landscape');
			// Render the HTML as PDF
			$dompdf->render();
			// Output the generated PDF to Browser
			$dompdf->stream('file.pdf', array( 'Attachment'=>0 ) );
		}

function print_testimonial($cgpa, $student, $profile ) {
	$db_db = new App\db\Db();
	$institute = $db_db->assign(array('key' => 'institute'))->get_value();
	$institute = is_array($institute)?$institute['_value']:'SAIC NURSING COLLEGE';
	$student_name = !empty($student['student_name'])?$student['student_name']: 'N/A';
	$father_name = !empty($profile['father_name'])?$profile['father_name']: 'N/A';
	$mother_name = !empty($profile['mother_name'])?$profile['mother_name']: 'N/A';
	$date_of_birth = !empty($profile['date_of_birth'])? date('d F Y', strtotime($profile['date_of_birth'])): 'N/A';
	$roll = !empty($student['roll'])?$student['roll']: 'N/A';
	$session = !empty($student['session'])?$student['session']: 'N/A';
	$bnc_roll = !empty($student['bnc_roll'])?$student['bnc_roll']: 'N/A';
	$address = $db_db->assign(array('key' => 'college_address'))->get_value();
	$address = is_array($address)?$address['_value']:'';
	
	$start_date = !empty($student['start_date'])?$student['start_date']: 'N/A';
	$end_date = !empty($student['end_date'])?$student['end_date']: 'N/A';
	$final_exam_date = !empty($student['final_exam_date'])?$student['final_exam_date']: 'N/A';
	
	//address
	$all_address = !empty($profile['permanent_address'])?$profile['permanent_address']: '';
    $permanent_address = '';
    $po = '';
    $ps = '';
    $district = '';

    if (!empty($all_address)) {
      $all_address = json_decode($all_address, true);

      if (isset($all_address['permanent_address'])) {
        $permanent_address = $all_address['permanent_address'];
      }
      if (isset($all_address['ps'])) {
        $ps = $all_address['ps'];
      }
      if (isset($all_address['po'])) {
        $po = $all_address['po'];
      }
      if (isset($all_address['district'])) {
        $district = $all_address['district'];
      }
    }

	//$student_name = 'Abcdefhj Isjahkdss Iosksnmn sgtFshsksg';

	$str = '<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style>
		@font-face {
		    font-family: f1;
		    src: url(fonts/f1.ttf);
		}
		@font-face {
		    font-family: f2;
		    src: url(fonts/f2.ttf);
		}
		@font-face {
		    font-family: f3;
		    src: url(fonts/f3.otf);
		}
		@font-face {
		    font-family: f4;
		    src: url(fonts/f4.ttf);
		}
		@font-face {
		    font-family: f5;
		    src: url(fonts/f5.ttf);
		}
		@font-face {
		    font-family: f6;
		    src: url(fonts/f6.ttf);
		}
		body {
			min-height: 500px;
			background: url(frame3.png);
    		background-repeat: no-repeat;
			width: 1050px;
		}
		.head {
			line-height: 30px;
		}
		.logo {
			position: absolute;
			left: 100px;
			top: 60px;
		}
		.head-title {
			text-align: center;
			padding-top: 45px;
		}
		.name {
			font-size: 36px;
			color: #0072bc;
			font-family: f1;
		}
		.address{
			font-size: 15px;
			font-family: f2;
		}
		.title {
			font-family: f4;
			font-size: 22px;
			color: #00aeef;
		}

		.text {
			margin-left: 85px;
			margin-right: 80px;
			line-height: 30px;
		}
		.serial {
			/*font-family: f3;*/
			font-size: 12;
		}
		.text-body{
			font-family: f5;
			font-size: 14;
		}

		.footer {
			padding-left: 85px;
			margin-top: 40px;
		}
		.date {
			/*font-family: f3; */
			font-size: 12;
			float: left;
			width: 55%;
			padding-top: 30px;
		}
		.sign{
			font-family: f6;
			font-size: 14;
			width: 45%;
			float: left;
			text-align: center;
			padding-top: 10px;
		}
		.sign small {
			font-family: f2;
			font-size: 11;
		}
		.floating {
			border-bottom:1px dotted black;
			width: 100%;
		}

	</style>

</head>
<body>
	<div class="container">
		<div class="head">
			<div class="logo2">
			&nbsp;<img src="logo.png" class="logo" width="80" height="80">
			</div>
			<div class="head-title">
				<div class="name">
					'.$institute.'
				</div>
				<div class="address">
					'.$address.'
				</div>
				<div class="title">
					TESTIMONIAL
				</div>
			</div>
			
		</div>

		<div class="text">
			<div class="serial">
				SL No. :
			</div>
			<div class="text-body">
				This is to certify Mr./Miss'.(trim(get_floating_text($student_name, 25, -6, 250))).' ................................................................................................................................ <br>
				Son/Daughter of Mr'.(trim(get_floating_text($father_name, 25, -6, 266))).'........................................................ Mrs'.(trim(get_floating_text($mother_name, 25, -6, 150))).'.......................................................................... <br>
				Date of Birth'.(trim(get_floating_text($date_of_birth, 7, -6, 210))).'................................. Reg. No'.(trim(get_floating_text($bnc_roll, 15, -5))).'...............  Session'.(trim(get_floating_text($session, 20, -6))).'...................... Examination Roll No'.(trim(get_floating_text($roll, 20, -6, 100))).'................. <br>
				Permanent Address'.(trim(get_floating_text(substr($permanent_address,0,50), 20, -6, 200))).'.......................................................................................... P.O'.(trim(get_floating_text($po, 30, -6, 200))).'........................................ <br>
				PS'.(trim(get_floating_text($ps, 30, -6, 200))).'...........................................................  District'.(trim(get_floating_text($district, 40, -6, 200))).'........................................,Bangladesh was a student of the<br>
				'.(trim(get_floating_text($institute, 10, -6, 200))).'...................................................................  From '.(trim(get_floating_text($start_date, 20, -6, 200))).'.......................................   to'.(trim(get_floating_text($end_date, 20, -6))).' ............................................... <br>
				He/ She passed the Diploma in Nursing Science & Midwifery held in '.(trim(get_floating_text($final_exam_date, 30, -6, 200))).'............................................ obtaining CGPA '.(trim(get_floating_text($cgpa, 20, -6, 50))).'.............Scale of 4.To the best  of my knowledge, during the period of his/her study at this institution, he/she bear a good moral character and did not take part in any subversive activity of the state. I wish his/ her every success in life.  
			</div>
		</div>

		<div class="footer">
			<div class="date">
				Date:'.(trim(get_floating_text(date('d F Y', time()), 10, -7))).'....................................	
			</div>
			<div class="sign">
				Principal <br>
				<small>'.$institute.'</small>
			</div>
		</div>
	</div>
</body>
</html>';
return $str;
}

function get_floating_text($text, $left = 100, $top = -25, $min_width = 500) {
	return '<i style="position: relative; width: 0; height: 0;">
			    <i style="position: absolute; min-width: '.$min_width.'px; left: '.$left.'px; top: '.$top.'px;text-align:center;" >
			        '.$text.'
			    </i>
			</i>';


	// return '<span style="position: relative; width: 0; height: 0;">
	// 		    <span style="position: absolute; min-width: '.$min_width.'px; left: '.$left.'px; top: '.$top.'px;">
	// 		        '.$text.'
	// 		    </span>
	// 		</span>';
}

function get_cgpa($student, $profile) {
				
	$db_student = new App\student\Student();
	$db_course = new App\course\Course();
	$db_db = new App\db\Db();
	$settings = new App\settings\Settings();

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


	$student_name = !empty($student['student_name'])?$student['student_name']: 'N/A';
	$father_name = !empty($profile['father_name'])?$profile['father_name']: 'N/A';
	$mother_name = !empty($profile['mother_name'])?$profile['mother_name']: 'N/A';
	$roll = !empty($student['roll'])?$student['roll']: 'N/A';
	$bnc_roll = !empty($student['bnc_roll'])?$student['bnc_roll']: 'N/A';
	$session = !empty($student['session'])?$student['session']: 'N/A';
	$date_of_birth = !empty($profile['date_of_birth'])? date('d F Y', strtotime($profile['date_of_birth'])): 'N/A';
	$institute = $db_db->assign(array('key' => 'institute'))->get_value();
	$code = $db_db->assign(array('key' => 'college_code'))->get_value();
	$clg_subject = $db_db->assign(array('key' => 'college_subject'))->get_value();

	//print_r($code);
	//die();
   //html page starts 

   //st for year 1,2, and 3
   	$st = array(1,2,3);

    //configuration
	$student_id = $student['student_id'];
		
    	//table starts here

    //for all year check result and if available print them in table
	$final_pass_flag = true; //check if fail in any subject 
    foreach ($st as $y) {
    	$db = new App\marks\Marks();
    	$year = $y;
        $semester1 = $year.'-1';
	    $semester2 = $year.'-2';
        //check here is result available or not
    	if (!$db->assign(array('session' => $session, 'semester' => $semester1))->check_all()) {
    		continue;
    	} 	



	// start semester one
	$result1s = $db->assign(array('session' => $session, 'semester' => $semester1))->all_by_session_semester();
	// start semester two
	$db2 = new App\marks\Marks();
	$result2s = $db2->assign(array('session' => $session, 'semester' => $semester2))->all_by_session_semester();

	$total_point1 = 0;
    $total_credit1 = 0;
    $rowspan = 0;
    $flag = 0;

	$total_point2 = 0;
    $total_credit2 = 0;
    $rowspan2 = 0;
    $flag2 = 0;

    if (is_array($result1s)) {
    	//pre calculate total grade
	    foreach ($result1s as $result) {
	    	$my_result = unserialize($result['result']);
	    	if (array_key_exists($student_id, $my_result) && !empty($my_result[$student_id]) ) {
	            $point_value =  $result['credit']*$grade_map[$my_result[$student_id]];
	            $total_point1 += $point_value;
	          }
	            $total_credit1 += $result['credit'];
	    }
    }
//die();
    if (is_array($result2s)) {
    	//pre calculate total grade semester 2
	    foreach ($result2s as $result) {
	    	$my_result = unserialize($result['result']);
	    	if (array_key_exists($student_id, $my_result) && !empty($my_result[$student_id]) ) {
	            $point_value =  $result['credit']*$grade_map[$my_result[$student_id]];
	            $total_point2 += $point_value;
	          }
	            $total_credit2 += $result['credit'];
	    }
    }
    $rowspan = $db_course->assign(array('session' => $session, 'semester' => $semester1))->total_by_session_semester();
    $rowspan2 = $db_course->assign(array('session' => $session, 'semester' => $semester2))->total_by_session_semester();
    //extra one space for total row
    if ($rowspan != 0) {
    	$rowspan++;
    }
    if ($rowspan2 != 0) {
    	$rowspan2++;
    }
    $grade_point1 = $total_credit1>0?round($total_point1/$total_credit1, 2): 0;
    $grade_point2 = $total_credit2>0? round($total_point2/$total_credit2, 2): 0;
    $final_grade = $total_credit2>0?round( ($total_point1 + $total_point2) / ($total_credit1 + $total_credit2),2):0;
    //for final table
    $summery[$year]['credit'] = $total_credit1 + $total_credit2;
    $summery[$year]['point'] = $total_point1 + $total_point2;
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
        if($point_value == 0) {
        	$final_pass_flag = false;
        }
      }else{
        $point_value = 'N/A';
      }
          $flag = 1;
    } //end foreach

    if ($total_credit1 != 0) {
    }
    //end semester one


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
        $flag2 = 1;
    } //end foreach

    if ($total_credit2 != 0) {
      }
    //end semester two
		 } //end year foreach

	//validation
	for ($i=1; $i <= 3; $i++) { 
		if (!isset($summery[$i]['credit'])) {
			$summery[$i]['credit'] = 0;
		}
		if (!isset($summery[$i]['point'])) {
			$summery[$i]['point'] = 0;
		}
	}

	//calculation
	$summery_total_credit = $summery['1']['credit']+$summery['2']['credit']+$summery['3']['credit'];
	$summery_total_point = $summery['1']['point']+$summery['2']['point']+$summery['3']['point'];

	if ($summery_total_credit != 0 || $summery_total_point != 0) {
		//summary table
	}//if valid result

	//check for all 3 year
	if($summery['1']['point']>0 && $summery['2']['point']>0 && $summery['1']['point']> 0) {
		//echo 'completed';
		//summery
		if ($summery_total_credit >0) {
			$letter_grade = $grade_map_r[floor($summery_total_point/$summery_total_credit)];
			$gpa = round($summery_total_point/$summery_total_credit, 2);
			if ($letter_grade == 'F' || !$final_pass_flag) {
				$letter_grade = 'FAIL';
				return false;
			}else{
				$letter_grade = 'PASS';
				return $gpa;
			}
		}
	}else{
		//echo 'Not completed';
		return false;
	}
return false;
}

?>
