<?php 
	include_once ('../../vendor/autoload.php');
	$db = new App\student\Student();
	$db_db = new App\db\Db();
	$settings = new App\settings\Settings();

	if (session_status() == PHP_SESSION_NONE) {
	    session_start();
	}
	//if not logged in redirect him
	if (!isset($_SESSION['user_roll'])) {
		header("Location: ../home/login.php");
	}


	//TODO: get user role from session or DB
	$user_role = $_SESSION['user_roll'];
	$operation = 'student-add';

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}

	$institute = $db_db->assign(array('key' => 'institute'))->get_value();
  	$code = $db_db->assign(array('key' => 'college_code'))->get_value();

  	$institute = is_array($institute)?$institute['_value']:'';
  	$code = is_array($code)?$code['_value']:'';


//start image upload
// Check if image file is a actual image or fake image
if (isset($_FILES['file']) && !empty($_POST['session']) && !empty($_POST['student_id'])) {

	$target_dir = "/../../assets/images/students/";

	//check for folder
	if (!file_exists(dirname( __FILE__ ).$target_dir.$_POST['session'])) {
	    mkdir(dirname( __FILE__ ) . $target_dir.$_POST['session']);
	}
	$target_dir = $target_dir.$_POST['session'].'/';

	$target_file = dirname( __FILE__ ). $target_dir . uniqid() .'.' . pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

	if (!empty($_FILES["file"]["tmp_name"])) {
		$check = getimagesize($_FILES["file"]["tmp_name"]);
		if ($check !== false) {
        //echo "File is an image - " . $check["mime"] . ".";
        	$uploadOk = 1;
	    } else {
	        echo "Unsupported image.";
	        $uploadOk = 0;
	    }
	}else{
		echo "Unsupported image.";
	    $uploadOk = 0;
	}    

	// Check if file already exists
	if (file_exists($target_file)) {
	    echo "File already exists.";
	    $uploadOk = 0;
	}
	
	// Check file size
	if (($_FILES["file"]["size"]/1024) > (1024*1)) {
	    echo "Your file is too large.Max size 1MB.";
	    $uploadOk = 0;
	}
	// Allow certain file formats
	if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
	    echo "Only JPG, JPEG, PNG & GIF files are allowed.";
	    $uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
	    echo "<br>Sorry, your file was not uploaded.";

	// if everything is ok, try to upload file
	} else {
	    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {

	        $data['photo'] = basename($target_file);
	        $data['student_id'] = $_POST['student_id'];
	        //delete old photo
	        $student = $db->assign($data)->single_profile();
	        try{
	        	if (!empty($student['photo']) ) {
	        		$path = $target_dir . $student['photo'];
				    if( file_exists( dirname( __FILE__ ).$path ) && is_writable( dirname( __FILE__ ).$path ) ){
				      unlink( dirname( __FILE__ ).$path ); 
				    }else{
				    	echo 'file is not deleted';
				    }
	        	}
			} catch (Exception $exc) {
			    echo $exc->getMessage();
			    echo $exc->getTraceAsString();
			}

	        $db->assign($data)->update_photo();

	        if (true) {
	            echo "Successfully uploaded";
	        } else {
	            echo "Error in Data Submitting";
	        }
	    } else {
	         echo "Sorry, there was an error uploading your photo.";
	    }
	}
}
//end image upload section


	if(isset($_REQUEST)){

		if(!empty($_POST['home'])) {
			//first form
			$filter = new App\filter\Filter();
  			$validator = $filter->student_basic();

			if($validator->is_valid($_REQUEST)) {
				$data['student_id'] = uniqid('', true).''.rand().''.rand();
				$data['student_name'] = $_REQUEST['student_name'];
				$data['roll'] = $_REQUEST['roll'];
				$data['bnc_roll'] = $_REQUEST['bnc_roll'];
				$data['session'] = $_REQUEST['session'];
				$data['semester'] = $_REQUEST['semester'];
				
				$data['start_date'] = $_REQUEST['start_date'];
				$data['end_date'] = $_REQUEST['end_date'];
				$data['final_exam_date'] = $_REQUEST['final_exam_date'];
				
				if ($db->assign($data)->check_by_roll2()) {
  					$_SESSION['error_msg'] = 'This roll number already exists.';
					header('Location: ../student/student-add.php');
	  			}else{
					$db->assign($data)->store();
	  			}
			}else{
				//print_r($validator->get_errors());
				$_SESSION['error_msg'] = 'Oops! That wasn\'t supposed to happen.';
				header('Location: ../student/student-add.php');
			}
		}

		if(!empty($_POST['profile'])) {
			//custom validation 

			if(true) {
				$data['student_id'] 		= $_POST['student_id'];
			    $data['institute_name'] 	= $institute;
			    $data['college_code'] 		= $code;
			    $data['father_name'] 		= $_POST['father_name'];
			    $data['mother_name'] 		= $_POST['mother_name'];
			    $data['guardian_name'] 		= $_POST['guardian_name'];
			    $data['relation_to_guardian'] = $_POST['relation_to_guardian'];
			    $data['date_of_birth'] 		= $_POST['date_of_birth'];
			    $data['nid'] 				= $_POST['nid'];
			    $data['contact_number'] 	= $_POST['contact_number'];
			    $data['nationality'] 		= $_POST['nationality'];
			    $data['marital_status'] 	= $_POST['marital_status'];
			    $data['gender'] 			= isset($_POST['gender'])?$_POST['gender']: null;
			    $data['religion'] 			= $_POST['religion'];
			    $data['service_type'] 		= $_POST['service_type'];
			    $data['permanent_address'] 	= $_POST['permanent_address'];
			    $data['current_address'] 	= $_POST['current_address'];
			    $data['school_name'] 		= $_POST['school_name'];
			    $data['college_name'] 		= $_POST['college_name'];
			    $data['ssc_gpa'] 			= $_POST['ssc_gpa'];
			    $data['hsc_gpa'] 			= $_POST['hsc_gpa'];
			    $data['ssc_passing_year'] 	= $_POST['ssc_passing_year'];
			    $data['hsc_passing_year'] 	= $_POST['hsc_passing_year'];
			    $data['original_ssc_doc'] 	= $_POST['original_ssc_doc'];
			    $data['original_hsc_doc'] 	= $_POST['original_hsc_doc'];

			    /* Updated Dec 2017*/
			    $permanent_address = array(
			    	'permanent_address' => $data['permanent_address'],
			    	'ps' => $_POST['ps'],
			    	'po' => $_POST['po'],
			    	'district' => $_POST['district'],
			    );
			    $data['permanent_address'] = json_encode($permanent_address);

			    $db->assign($data)->store_profile(1);
			}else{
				//print_r($validator->get_errors());
				$_SESSION['error_msg'] = 'Oops! That wasn\'t supposed to happen.';
				header('Location: ../student/student-add.php');
			}
		}
		// /$db->assign($data)->store();
	}


// 	if(isset($_FILES['file'])){
// 		$errors = array();
// 		$filename = time().$_FILES['file']['name'];
// 		$filetype = $_FILES['file']['type'];
// 		$filetemp = $_FILES['file']['tmp_name'];
// 		$filesize = $_FILES['file']['size'];
// 		$fileext = strtolower(end(explode('.', $_FILES['file']['name'])));

// 		$formats = array("jpeg","jpg","png");

// 		if(in_array($fileext, $formats) === false){
// 			$errors[]="Extension not allowed";
// 		}

// 		if($filesize>2097152){
// 			$errors[]="File size must be excately 2 MB";
// 		}

// 		if(empty($errors) == true){
// 			move_uploaded_file($filetemp, "../../img/".$filename);
// 			$_POST['img']=$filename;
// 		}else{
// 			print_r($errors);
// 		}
// }

?>