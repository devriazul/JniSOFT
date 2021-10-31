<?php

namespace App\Student;

use PDO;
use PDOException;
use App\connection;
class Student {

    public $student_id, $student_name, $roll, $bnc_roll, $session, $semester, $start_date, $end_date, $final_exam_date;
    
    public $institute_name, $college_code, $father_name, $mother_name, $guardian_name, $relation_to_guardian, $date_of_birth, $nid, $contact_number, $nationality, $marital_status, $gender, $religion, $service_type, $permanent_address, $current_address, $photo, $school_name, $college_name, $ssc_gpa, $hsc_gpa, $ssc_passing_year, $hsc_passing_year, $original_ssc_doc, $original_hsc_doc, $created_at, $updated_at, $deleted_at;
    public $conn;

    public function __construct() {
         if (session_status() == PHP_SESSION_NONE) {
            session_start();
         }
        try {
            $db = new connection\Connection();
            $this->conn = new PDO('mysql:host='.$db->host.';dbname='. $db->db_name, $db->user, $db->password);
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // assgin basic student data
    public function assign($data) {
        //basic information
        if (!empty($data['student_id'])) {
            $this->student_id = $data['student_id'];
        }
        if (!empty($data['student_name'])) {
            $this->student_name = $data['student_name'];
        }
        if (!empty($data['roll'])) {
            $this->roll = $data['roll'];
        }
        if (!empty($data['bnc_roll'])) {
            $this->bnc_roll = $data['bnc_roll'];
        }
        if (!empty($data['session'])) {
            $this->session = $data['session'];
        }
        if (!empty($data['semester'])) {
            $this->semester = $data['semester'];
        }
		if (!empty($data['start_date'])) {
            $this->start_date = $data['start_date'];
        }
		if (!empty($data['end_date'])) {
            $this->end_date = $data['end_date'];
        }
		if (!empty($data['final_exam_date'])) {
            $this->final_exam_date = $data['final_exam_date'];
        }

        //profile data
        if (!empty($data['institute_name'])) {
            $this->institute_name = $data['institute_name'];
        }
        if (!empty($data['college_code'])) {
            $this->college_code = $data['college_code'];
        }
        if (!empty($data['father_name'])) {
            $this->father_name = $data['father_name'];
        }
        if (!empty($data['mother_name'])) {
            $this->mother_name = $data['mother_name'];
        }
        if (!empty($data['guardian_name'])) {
            $this->guardian_name = $data['guardian_name'];
        }
        if (!empty($data['relation_to_guardian'])) {
            $this->relation_to_guardian = $data['relation_to_guardian'];
        }
        if (!empty($data['date_of_birth'])) {
            $this->date_of_birth = $data['date_of_birth'];
        }
        if (!empty($data['nid'])) {
            $this->nid = $data['nid'];
        }
        if (!empty($data['contact_number'])) {
            $this->contact_number = $data['contact_number'];
        }
        if (!empty($data['nationality'])) {
            $this->nationality = $data['nationality'];
        }
        if (!empty($data['marital_status'])) {
            $this->marital_status = $data['marital_status'];
        }
        if (!empty($data['gender'])) {
            $this->gender = $data['gender'];
        }
        if (!empty($data['religion'])) {
            $this->religion = $data['religion'];
        }
        if (!empty($data['service_type'])) {
            $this->service_type = $data['service_type'];
        }
        if (!empty($data['permanent_address'])) {
            $this->permanent_address = $data['permanent_address'];
        }
        if (!empty($data['current_address'])) {
            $this->current_address = $data['current_address'];
        }
        if (!empty($data['photo'])) {
            $this->photo = $data['photo'];
        }
        if (!empty($data['school_name'])) {
            $this->school_name = $data['school_name'];
        }
        if (!empty($data['college_name'])) {
            $this->college_name = $data['college_name'];
        }
        if (!empty($data['ssc_gpa'])) {
            $this->ssc_gpa = $data['ssc_gpa'];
        }
        if (!empty($data['hsc_gpa'])) {
            $this->hsc_gpa = $data['hsc_gpa'];
        }
        if (!empty($data['ssc_passing_year'])) {
            $this->ssc_passing_year = $data['ssc_passing_year'];
        }
        if (!empty($data['hsc_passing_year'])) {
            $this->hsc_passing_year = $data['hsc_passing_year'];
        }
        if (!empty($data['original_ssc_doc'])) {
            $this->original_ssc_doc = $data['original_ssc_doc'];
        }
        if (!empty($data['original_hsc_doc'])) {
            $this->original_hsc_doc = $data['original_hsc_doc'];
        }

        return $this;
    }

    // add student basic info  in `student` table
    public function store() {
        if(!empty($this->student_id) && !empty($this->student_name)  && !empty($this->roll) && !empty($this->session) && !empty($this->semester) ){
            try {
                $query = "INSERT INTO student (student_id, student_name, roll, bnc_roll, session, semester, start_date, end_date, final_exam_date, created_at, updated_at, deleted_at) VALUES
                                            (:student_id, :student_name, :roll, :bnc_roll, :session, :semester, :start_date, :end_date, :final_exam_date,  now(), :updated_at, :deleted_at )";
                $q = $this->conn->prepare($query);
                $q->execute(array(
                    ':student_id'    => $this->student_id,
                    ':student_name'  => $this->student_name,
                    ':roll'          => $this->roll,
                    ':bnc_roll'      => $this->bnc_roll,
                    ':session'       => $this->session,
                    ':semester'      => $this->semester,
					'start_date' 		=> $this->start_date,
					'end_date' 			=> $this->end_date,
					'final_exam_date' 	=> $this->final_exam_date,
                    ':updated_at'    => null,
                    ':deleted_at'    => null,
                    ));
                $rows = $q->rowCount();
                //$e = $q->errorInfo();
                //$_SESSION['error_msg'] = $e[2];
                if($rows>0){
                    $_SESSION['student_id'] = $this->student_id;
                    $_SESSION['success_msg'] = "Student Successfully Added";
                    header('Location: ../../view/student/student-add.php');
                } else {
                    $e = $q->errorInfo();
                    $_SESSION['error_msg'] = 'Student is not added.Unexpected error'. $e[2];
                    header('Location: ../../view/student/student-add.php');
                }
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }else{
            //empty data
            $_SESSION['error_msg'] = "Please enter all the required information";
            header('Location: ../../view/student/student-add.php');
        }
        return $this;
    }

    // add student student profile
    // $redirect_to_update  is used when we want to redirect user to update profile page
    // after creating profile
    public function store_profile($redirect_to_update = null) {
        if(!empty($this->student_id) ){
            try {
                $query = "INSERT INTO student_profile (student_id, institute_name, college_code, father_name, mother_name, guardian_name, relation_to_guardian, date_of_birth, nid, contact_number, nationality, marital_status, gender, religion, service_type, permanent_address, current_address, photo, school_name, college_name, ssc_gpa, hsc_gpa, ssc_passing_year, hsc_passing_year, original_ssc_doc, original_hsc_doc, created_at, updated_at, deleted_at ) 
                                               VALUES (:student_id, :institute_name, :college_code, :father_name, :mother_name, :guardian_name, :relation_to_guardian, :date_of_birth, :nid, :contact_number, :nationality, :marital_status, :gender, :religion, :service_type, :permanent_address, :current_address, :photo, :school_name, :college_name, :ssc_gpa, :hsc_gpa, :ssc_passing_year, :hsc_passing_year, :original_ssc_doc, :original_hsc_doc, now(), :updated_at, :deleted_at )";
                $q = $this->conn->prepare($query);
                $q->execute(array(
                    ':student_id'               => $this->student_id,
                    ':institute_name'           => $this->institute_name,
                    ':college_code'             => $this->college_code,
                    ':father_name'              => $this->father_name,
                    ':mother_name'              => $this->mother_name,
                    ':guardian_name'            => $this->guardian_name,
                    ':relation_to_guardian'     => $this->relation_to_guardian,
                    ':date_of_birth'            => date("Y-m-d H:i:s", strtotime($this->date_of_birth)),
                    ':nid'                      => $this->nid,
                    ':contact_number'           => $this->contact_number,
                    ':nationality'              => $this->nationality,
                    ':marital_status'           => $this->marital_status,
                    ':gender'                   => $this->gender,
                    ':religion'                 => $this->religion,
                    ':service_type'             => $this->service_type,
                    ':permanent_address'        => $this->permanent_address,
                    ':current_address'          => $this->current_address,
                    ':photo'                    => '',
                    ':school_name'              => $this->school_name,
                    ':college_name'             => $this->college_name,
                    ':ssc_gpa'                  => $this->ssc_gpa,
                    ':hsc_gpa'                  => $this->hsc_gpa,
                    ':ssc_passing_year'         => $this->ssc_passing_year,
                    ':hsc_passing_year'         => $this->hsc_passing_year,
                    ':original_ssc_doc'         => $this->original_ssc_doc,
                    ':original_hsc_doc'         => $this->original_hsc_doc,
                    ':updated_at'               => null,
                    ':deleted_at'               => null,
                    ));

                //check if any error in query
                //print_r($q->errorInfo());
                $rows = $q->rowCount();
                if($rows>0){
                    $_SESSION['success_msg'] = "Student Profile Successfully created";
                    if ($redirect_to_update == 1) {
                        $_SESSION['student_id'] = $this->student_id;
                        header('Location: ../../view/student/student-edit.php?student='.$this->student_id);
                    }else{
                        header('Location: ../../view/student/student-add.php');
                    }
                } else {
                    $_SESSION['error_msg'] = "Student Profile  is not created.Unexpected error";
                    if ($redirect_to_update == 1) {
                        $_SESSION['student_id'] = $this->student_id;
                        header('Location: ../../view/student/student-edit.php?student='.$this->student_id);
                    }else{
                        header('Location: ../../view/student/student-add.php');
                    }
                }
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }else{
            //empty data
            $_SESSION['error_msg'] = "Please enter all the required information";
            if ($redirect_to_update == 1) {
                header('Location: ../../view/student/student-edit.php?student='.$this->student_id);
            }else{
                header('Location: ../../view/student/student-add.php');
            }
        }
        return $this;
    }

    //Update student basic info
    public function update(){
        if(!empty($this->student_id) && !empty($this->student_name) && !empty($this->session) && !empty($this->semester) ){
            $qry = "UPDATE student SET student_name = :student_name, bnc_roll = :bnc_roll, session = :session, semester = :semester, start_date = :start_date, end_date = :end_date, final_exam_date = :final_exam_date, updated_at = now() WHERE student_id = :student_id";
            $q = $this->conn->prepare($qry) or die("Failed");
            $q->execute(array(
                ':student_id'   => $this->student_id,
                ':student_name' => $this->student_name,
                ':bnc_roll'     => $this->bnc_roll,
                ':session'      => $this->session,
                ':semester'     => $this->semester,
				'start_date' 		=> $this->start_date,
				'end_date' 			=> $this->end_date,
				'final_exam_date' 	=> $this->final_exam_date
                ));
            if($q){
                $_SESSION['success_msg'] = "Student data has been updated successfully";
                header('Location: ../../view/student/student-edit.php?student='.$this->student_id);
            }else{
                $_SESSION['error_msg'] = "Student is not updated.Unexpected error.";
                header('Location: ../../view/student/student-edit.php?student='.$this->student_id);
            }
        }else {
            //empty data
            $_SESSION['error_msg'] = "Please enter all the required information";
            header('Location: ../../view/student/student-edit.php?student='.$this->student_id);
        }
        return $this;
    }

    //Update student profile info
    public function update_profile(){
        if(!empty($this->student_id) ){
            $qry = "UPDATE student_profile SET institute_name = :institute_name, college_code = :college_code, father_name = :father_name, mother_name = :mother_name, guardian_name = :guardian_name, relation_to_guardian = :relation_to_guardian, date_of_birth = :date_of_birth, nid = :nid, contact_number = :contact_number, nationality = :nationality, marital_status = :marital_status, gender = :gender, religion = :religion, service_type = :service_type, permanent_address = :permanent_address, current_address = :current_address, school_name = :school_name, college_name = :college_name, ssc_gpa = :ssc_gpa, hsc_gpa = :hsc_gpa, ssc_passing_year = :ssc_passing_year, hsc_passing_year = :hsc_passing_year, original_ssc_doc = :original_ssc_doc, original_hsc_doc = :original_hsc_doc, updated_at = now() WHERE student_id = :student_id";
            $q = $this->conn->prepare($qry) or die("Failed");
            $q->execute(array(
                ':student_id'                   => $this->student_id,
                    ':institute_name'           => $this->institute_name,
                    ':college_code'             => $this->college_code,
                    ':father_name'              => $this->father_name,
                    ':mother_name'              => $this->mother_name,
                    ':guardian_name'            => $this->guardian_name,
                    ':relation_to_guardian'     => $this->relation_to_guardian,
                    ':date_of_birth'            => date("Y-m-d H:i:s", strtotime($this->date_of_birth)),
                    ':nid'                      => $this->nid,
                    ':contact_number'           => $this->contact_number,
                    ':nationality'              => $this->nationality,
                    ':marital_status'           => $this->marital_status,
                    ':gender'                   => $this->gender,
                    ':religion'                 => $this->religion,
                    ':service_type'             => $this->service_type,
                    ':permanent_address'        => $this->permanent_address,
                    ':current_address'          => $this->current_address,
                    ':school_name'              => $this->school_name,
                    ':college_name'             => $this->college_name,
                    ':ssc_gpa'                  => $this->ssc_gpa,
                    ':hsc_gpa'                  => $this->hsc_gpa,
                    ':ssc_passing_year'         => $this->ssc_passing_year,
                    ':hsc_passing_year'         => $this->hsc_passing_year,
                    ':original_ssc_doc'         => $this->original_ssc_doc,
                    ':original_hsc_doc'         => $this->original_hsc_doc,
                ));
            if($q){
                $_SESSION['student_id'] = $this->student_id;
                $_SESSION['success_msg'] = "Student profile has been updated successfully";
                header('Location: ../../view/student/student-edit.php?student='.$this->student_id);
            }else{
                $_SESSION['student_id'] = $this->student_id;
                $_SESSION['error_msg'] = "Student profile is not updated.Unexpected error.";
                header('Location: ../../view/student/student-edit.php?student='.$this->student_id);
            }
        }else {
            //empty data
            $_SESSION['student_id'] = $this->student_id;
            $_SESSION['error_msg'] = "Please enter all the required information";
            header('Location: ../../view/student/student-edit.php?student='.$this->student_id);
        }
        return $this;
    }

    //Update student profile picture
    public function update_photo(){
        if(!empty($this->student_id) && !empty($this->photo) ){
            $qry = "UPDATE student_profile SET photo = :photo, updated_at = now() WHERE student_id = :student_id";
            $q = $this->conn->prepare($qry) or die("Failed");
            $q->execute(array(
                ':photo' => $this->photo,
                ':student_id'   => $this->student_id
                ));
            if($q){
                //$_SESSION['success_msg'] = "Photo has been uploaded successfully";
                //header('Location: ../../view/student/student-edit.php?student='.$this->student_id);
            }else{
                //$_SESSION['error_msg'] = "Photo is not uploaded.Unexpected error.";
                //header('Location: ../../view/student/student-edit.php?student='.$this->student_id);
            }
        }else {
            //empty data
            //$_SESSION['error_msg'] = "Please enter all the required information";
            //header('Location: ../../view/student/student-edit.php?student='.$this->student_id);
        }
        return $this;
    }

    // get all student data
    public function all($limit, $offset){    
       try {
            $Query = "SELECT * FROM student ORDER BY created_at ASC LIMIT $limit OFFSET $offset";
            $q = $this->conn->query($Query);
            $rowCount = $q->rowCount();
            if ($rowCount > 0) {
                while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
                    $this->data[] = $row;
                }
                return $this->data;
            } else {
                return $rowCount;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $this;
    }

    // get all student data shorted by session
    public function all_student_session($limit, $offset, $session = null){    
       try {
            $Query = "SELECT * FROM student WHERE session = :session ORDER BY created_at ASC LIMIT $limit OFFSET $offset";
            $q = $this->conn->prepare($Query) or die("Failed");
            $q->execute(array(
                ':session'   => $this->session
                )
            );

            $rowCount = $q->rowCount();
            if ($rowCount > 0) {
                while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
                    $this->data[] = $row;
                }
                return $this->data;
            } else {
                return $rowCount;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $this;
    }

    public function all_student_session_full(){    
       try {
            $Query = "SELECT * FROM student WHERE session = :session ORDER BY roll ASC";
            $q = $this->conn->prepare($Query) or die("Failed");
            $q->execute(array(
                ':session'   => $this->session
                )
            );

            $rowCount = $q->rowCount();
            if ($rowCount > 0) {
                while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
                    $this->data[] = $row;
                }
                return $this->data;
            } else {
                return $rowCount;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $this;
    }

    // get all student data shorted by session & semester
    public function all_student_session_semester($limit, $offset){    
       try {
            $Query = "SELECT * FROM student WHERE session = :session AND semester = :semester ORDER BY created_at ASC LIMIT $limit OFFSET $offset";
            $q = $this->conn->prepare($Query) or die("Failed");
            $q->execute(array(
                ':session'   => $this->session,
                ':semester'   => $this->semester
                )
            );

            $rowCount = $q->rowCount();
            if ($rowCount > 0) {
                while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
                    $this->data[] = $row;
                }
                return $this->data;
            } else {
                return $rowCount;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $this;
    }

        // get all student data shorted by session & semester
    public function all_student_session_semester_full(){    
       try {
            $Query = "SELECT * FROM student WHERE session = :session AND semester = :semester ORDER BY roll ASC";
            $q = $this->conn->prepare($Query) or die("Failed");
            $q->execute(array(
                ':session'   => $this->session,
                ':semester'   => $this->semester
                )
            );

            $rowCount = $q->rowCount();
            if ($rowCount > 0) {
                while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
                    $this->data[] = $row;
                }
                return $this->data;
            } else {
                return $rowCount;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $this;
    }

    //search student without session
    public function search_by_roll(){    
       try {
            $Query = "SELECT * FROM student WHERE roll=:roll OR student_name Like '%".$this->roll."%' OR bnc_roll Like :roll";
            $q = $this->conn->prepare($Query) or die("Failed");
            $q->execute(array(
                ':roll'   => $this->roll
                )
            );

            $rowCount = $q->rowCount();
            if ($rowCount > 0) {
                while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
                    $this->data[] = $row;
                }
                return $this->data;
            } else {
                return $rowCount;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $this;
    }

    //search student with session
    public function search_by_roll_session(){    
       try {
            $Query = "SELECT * FROM student WHERE roll=:roll AND session = :session";
            $q = $this->conn->prepare($Query) or die("Failed");
            $q->execute(array(
                ':roll'   => $this->roll,
                ':session'   => $this->session
                )
            );

            $rowCount = $q->rowCount();
            if ($rowCount > 0) {
                while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
                    $this->data[] = $row;
                }
                return $this->data;
            } else {
                return $rowCount;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $this;
    }

    //check if any student is in database or not
    public function check(){
        try {
            $chkQuery = "SELECT * FROM student WHERE student_id = :student_id";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':student_id' => $this->student_id,
                ));
            $rowCount = $q->rowCount();
            return $rowCount;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $this;
    }

    //check if any student is in database or not By roll
    public function check_by_roll(){
        try {
            $chkQuery = "SELECT * FROM student WHERE roll = :roll";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':roll' => $this->roll,
                ));
            $rowCount = $q->rowCount();
            return $rowCount;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $this;
    }

    //check if any student is in database or not By roll
    public function check_by_roll2(){
        try {
            $chkQuery = "SELECT * FROM student WHERE roll = :roll && session = :session";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':roll' => $this->roll,
                ':session' => $this->session
                ));
            $rowCount = $q->rowCount();
            return $rowCount;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $this;
    }

    //check if student profile is in database or not
    public function check_profile(){
        try {
            $chkQuery = "SELECT * FROM student_profile WHERE student_id = :student_id";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':student_id' => $this->student_id,
                ));
            $rowCount = $q->rowCount();
            return $rowCount;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $this;
    }

    //get total student number for pagination
    public function total(){
        try {
            $chkQuery = "SELECT * FROM student";
            $q = $this->conn->query($chkQuery);
            $rowCount = $q->rowCount();
            return $rowCount;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $this;
    }

    //get total student number for a session
    public function total_student_session($session = null){
        try {
            $Query = "SELECT * FROM student WHERE session = :session";
            $q = $this->conn->prepare($Query) or die('Unable to Query');
            $q->execute(array(
                ':session' => $this->session,
                ));
            $rowCount = $q->rowCount();
            return $rowCount;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $this;
    }

    //get total student number for session and semester
    public function total_student_session_semester(){
        try {
            $Query = "SELECT * FROM student WHERE session = :session AND semester = :semester";
            $q = $this->conn->prepare($Query) or die('Unable to Query');
            $q->execute(array(
                ':session' => $this->session,
                ':semester' => $this->semester,
                ));
            $rowCount = $q->rowCount();
            return $rowCount;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $this;
    }


    //get Single student information
    public function single(){
        try {
            $Query = "SELECT * FROM student WHERE student_id = :student_id";
            $q = $this->conn->prepare($Query) or die('Unable to Query');
            $q->execute(array(
                ':student_id' => $this->student_id,
                ));
            $rowCount = $q->rowCount();
            if ($rowCount > 0) {
                $data = $q->fetch(PDO::FETCH_ASSOC);
                return $data;
            }else{
                return $rowCount;
            }

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $this;
    }

    //get Single student information by roll
    public function single_by_roll(){
        try {
            $Query = "SELECT * FROM student WHERE roll = :roll && session = :session";
            $q = $this->conn->prepare($Query) or die('Unable to Query');
            $q->execute(array(
                ':roll' => $this->roll,
                ':session' => $this->session
                ));
            $rowCount = $q->rowCount();
            if ($rowCount > 0) {
                $data = $q->fetch(PDO::FETCH_ASSOC);
                return $data;
            }else{
                return $rowCount;
            }

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $this;
    }

    //get Single student profile
    public function single_profile(){
        try {
            $Query = "SELECT * FROM student_profile WHERE student_id = :student_id";
            $q = $this->conn->prepare($Query) or die('Unable to Query');
            $q->execute(array(
                ':student_id' => $this->student_id,
                ));
            $rowCount = $q->rowCount();
            if ($rowCount > 0) {
                $data = $q->fetch(PDO::FETCH_ASSOC);
                return $data;
            }else{
                return $rowCount;
            }

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $this;
    }

    //delete student name
    public function delete() {
        if ($this->check()) {
            //delete student basic info
            $qry = "DELETE FROM student WHERE student_id = :student_id";
            $q = $this->conn->prepare($qry) or die("Error");
            $q->execute(array(
                ':student_id' =>$this->student_id,
                ));

            //delete student profile
            $qry2 = "DELETE FROM student_profile WHERE student_id = :student_id";
            $q2 = $this->conn->prepare($qry2) or die("Error");
            $q2->execute(array(
                ':student_id' =>$this->student_id,
                ));

            if($q && $q2){
                $_SESSION['success_msg'] = "Student has been successfully deleted.";
                header('Location: ../../view/student/index.php');
            }else{
                $_SESSION['error_msg'] = "Student is not deleted. Unexpected error.";
                header('Location: ../../view/student/index.php');
            }
        }else{
            //student not found
            $_SESSION['error_msg'] = "Sorry!Student is not found.";
            header('Location: ../../view/student/index.php');
        }
    }
}
