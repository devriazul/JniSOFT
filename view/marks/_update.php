<?php 
	include_once ('../../vendor/autoload.php');
	if (session_status() == PHP_SESSION_NONE) {
	    session_start();
	}
	//if not logged in redirect him
	if (!isset($_SESSION['user_roll'])) {
		header("Location: ../home/login.php");
	}

	$db = new App\marks\Marks();
	$db_course = new App\course\Course();
	$db_student = new App\student\Student();
	$settings = new App\settings\Settings();

	//get user role from session
	$user_role = $_SESSION['user_roll'];
	$operation = 'marks-add';

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}
	//result map
	$grade_map = array(
		'A' => 4, 
		'B' => 3, 
		'C' => 2, 
		'D' => 1, 
		'F' => 0 
		);
	
	if(isset($_REQUEST)){
		$course = $db_course->assign(array('course_id' => $_REQUEST['course_id']))->single_course();

		//Filtering data
		$data['session'] = $_REQUEST['session'];
		$data['semester'] = $_REQUEST['semester'];
		$data['course_id'] = $_REQUEST['course_id'];

		$filter = new App\filter\Filter();
  		$validator = $filter->marks_add();

		if($validator->is_valid($_POST)) {
			unset($_SESSION['error_msg']);
			//convert to result array
			foreach ($_REQUEST['result'] as $student_id => $grade) {
				if ($db_student->assign(array('student_id'=> $student_id))->check()) {
					if(!empty($grade['0'])) {
						if(($grade['0'] == 'A' || $grade['0'] == 'B' || $grade['0'] == 'C' || $grade['0'] == 'D' || $grade['0'] == 'F')) {
							$result[$student_id] = $grade['0'];
						}else{
							$_SESSION['error_msg'] = 'Marks grade must be [A,B,C,D or F]';
						}
					}
				}
			}
			
			if (empty($_SESSION['error_msg'])) {
				//echo 'every thing ok';
				//check if any marks is already set                            
		         if ($db->assign($data)->check()) {
		         	$single = $db->assign($data)->single();
		         	$single = unserialize($single['result']);
		         	if (!is_array($result)) {
		         		$result = array();
		         	}
		         	$data['result'] = serialize(array_merge($single, $result));
		         	$db->assign($data)->update(1);
		         }else{
					$data['result']	= isset($result) && is_array($result) ?serialize($result):'';
		        	$db->assign($data)->store(1);
		         }

		         //Auto promote student
		         foreach ($_REQUEST['result'] as $student_id => $grade) {
					if ($db_student->assign(array('student_id'=> $student_id))->check()) {
						if(!empty($grade['0'])) {
							if(($grade['0'] == 'A' || $grade['0'] == 'B' || $grade['0'] == 'C' || $grade['0'] == 'D' || $grade['0'] == 'F')) {
								$result[$student_id] = $grade['0'];

								//promote student
	              				$db->assign(array('student_id' => $student_id, 'session' => $data['session'], 'semester' => $data['semester']))->promote_student();
							}
						}
					}
				}
			}else{
				header('Location: ../marks/marks-edit.php?session='.$_POST['session'].'&semester='.$_POST['semester'].'&course_id='.$_POST['course_id']);
			}

     	}else{
     		$_SESSION['error_msg'] = 'Oops! That wasn\'t supposed to happen.';
			header('Location: ../marks/marks-edit.php');
     	}
		
	}
?>