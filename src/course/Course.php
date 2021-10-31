<?php
namespace App\Course;

use PDO;
use PDOException;

use App\connection;

class Course {

    public $id, $course_id, $course_name, $course_code, $credit, $session, $semester;
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

    // assgin value
    public function assign($data) {

        if (!empty($data['course_id'])) {
            $this->course_id = $data['course_id'];
        }

        if (!empty($data['course_name'])) {
            $this->course_name = $data['course_name'];
        }

        if (!empty($data['course_code'])) {
            $this->course_code = $data['course_code'];
        }

        if (isset($data['credit'])) {
            $this->credit = $data['credit'];
        }

        if (isset($data['session'])) {
            $this->session = $data['session'];
        }

        if (!empty($data['semester'])) {
            $this->semester = $data['semester'];
        }

        return $this;
    }

    // add course name in `course` table
    public function store() {
        if(!empty($this->course_name) && !empty($this->course_code) && !empty($this->session) && isset($this->semester)){
            try {
                $query = "INSERT INTO course (course_id, course_name, course_code, credit, session, semester, created_at, updated_at, deleted_at) VALUES
                                            (:course_id, :course_name, :course_code, :credit, :session, :semester, now(), :updated_at, :deleted_at )";
                $q = $this->conn->prepare($query);
                $q->execute(array(
                    ':course_id'    => $this->course_id,
                    ':course_name'  => $this->course_name,
                    ':course_code'  => $this->course_code,
                    ':credit'       => $this->credit,
                    ':session'       => $this->session,
                    ':semester'        => $this->semester,
                    ':updated_at'   => null,
                    ':deleted_at'   => null,
                    ));
                $rows = $q->rowCount();
                if($rows>0){
                    $_SESSION['success_msg'] = "Course Successfully Added";
                    header('Location: ../../view/course/course-add.php');
                } else {
                    $_SESSION['error_msg'] = "Course is not added.  Unexpected error";
                    header('Location: ../../view/course/course-add.php');
                }
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }else{
            //empty data
            $_SESSION['error_msg'] = "Please enter all the required information";
            header('Location: ../../view/course/course-add.php');
        }
        return $this;
    }

        // add course name in `course` table
    public function store_single() {
        if(!empty($this->course_name) && !empty($this->course_code) && !empty($this->session) && isset($this->semester)){
            try {
                $query = "INSERT INTO course (course_id, course_name, course_code, credit, session, semester, created_at, updated_at, deleted_at) VALUES
                                            (:course_id, :course_name, :course_code, :credit, :session, :semester, now(), :updated_at, :deleted_at )";
                $q = $this->conn->prepare($query);
                $q->execute(array(
                    ':course_id'    => $this->course_id,
                    ':course_name'  => $this->course_name,
                    ':course_code'  => $this->course_code,
                    ':credit'       => $this->credit,
                    ':session'       => $this->session,
                    ':semester'        => $this->semester,
                    ':updated_at'   => null,
                    ':deleted_at'   => null,
                    ));
                $rows = $q->rowCount();
                if($rows>0){
                    //$_SESSION['success_msg'] = "Course Successfully Added";
                    //header('Location: ../../view/course/course-add.php');
                } else {
                    //$_SESSION['error_msg'] = "Course is not added.  Unexpected error";
                    //header('Location: ../../view/course/course-add.php');
                }
            } catch (PDOException $e) {
                //echo 'Error: ' . $e->getMessage();
            }
        }else{
            //empty data
            //$_SESSION['error_msg'] = "Please enter all the required information";
            //header('Location: ../../view/course/course-add.php');
        }
        return $this;
    }

    //Update course info
    public function update(){
        if(!empty($this->course_code)){
            // session = :session, semester = :semester,
            $qry = "UPDATE course SET course_code = :course_code, credit = :credit, updated_at = now() WHERE course_id = :course_id";
            $q = $this->conn->prepare($qry) or die("Failed");
            $q->execute(array(
                ':course_code'  => $this->course_code,
                ':credit'       => $this->credit,
                //':session'       => $this->session,
                //':semester'     => $this->semester,
                ':course_id'    => $this->course_id
                ));
            if($q){
                $_SESSION['success_msg'] = "Course data has been updated successfully";
                header('Location: ../../view/course/course-edit.php?course='.$this->course_id);
            }else{
                $_SESSION['error_msg'] = "Course is not updated. Unexpected error.";
                header('Location: ../../view/course/course-edit.php?course='.$this->course_id);
            }
        }else {
            //empty data
            $_SESSION['error_msg'] = "Please enter all the required information";
            header('Location: ../../view/course/course-edit.php?course='.$this->course_id);
        }
        return $this;
    }

    // get all course
    public function all_course($limit, $offset){    
       try {
            $Query = "SELECT * FROM course ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
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


       // get all course
    public function all_distinct_course(){    
       try {
            $Query = "SELECT * FROM course group by course_name ORDER BY semester ASC";
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

    // get course by session and semester
    public function all_course_by_session($limit, $offset){    
       try {
            $Query = "SELECT * FROM course WHERE session = :session ORDER BY semester ASC LIMIT $limit OFFSET $offset";
            $q = $this->conn->prepare($Query);
                $q->execute(array(
                    ':session'     => $this->session
                    ));
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

    // get course by session
    public function all_course_by_session_semester(){    
       try {
            $Query = "SELECT * FROM course WHERE session = :session AND semester = :semester ORDER BY course_name ASC";
            $q = $this->conn->prepare($Query);
                $q->execute(array(
                    ':session'     => $this->session,
                    ':semester'     => $this->semester
                    ));
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

    //check if any course is in database or not
    public function check(){
        try {
            $chkQuery = "SELECT * FROM course WHERE course_id = :course_id ";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':course_id' => $this->course_id,
                ));
            $rowCount = $q->rowCount();
            return $rowCount;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $this;
    }

    //check if any course is in database or not
    public function check_duplicate(){
        try {
            $chkQuery = "SELECT * FROM course WHERE course_name = :course_name AND session != :session";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':course_name' => $this->course_name,
                ':session' => $this->session
               ));
            $rowCount = $q->rowCount();
            return $rowCount;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $this;
    }

    //get total course number for pagination
    public function total_course_number(){
        try {
            $chkQuery = "SELECT * FROM course";
            $q = $this->conn->query($chkQuery);
            $rowCount = $q->rowCount();
            return $rowCount;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $this;
    }

    //get total course number for pagination
    public function total_distinc_course_number(){
        try {
            $chkQuery = "SELECT DISTINCT course_name FROM course";
            $q = $this->conn->query($chkQuery);
            $rowCount = $q->rowCount();
            return $rowCount;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $this;
    }

    //get total course number by session for pagination
    public function total_by_session(){
        try {
            $chkQuery = "SELECT * FROM course WHERE session = :session";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':session' => $this->session
                ));
            $rowCount = $q->rowCount();
            return $rowCount;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $this;
    }

    //get total course number by session and semester for pagination
    public function total_by_session_semester(){
        try {
            $chkQuery = "SELECT * FROM course WHERE session = :session AND semester = :semester";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':session' => $this->session,
                ':semester' => $this->semester
                ));
            $rowCount = $q->rowCount();
            return $rowCount;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $this;
    }

    //get Single course information
    public function single_course(){
        try {
            $Query = "SELECT * FROM course WHERE course_id = :course_id";
            $q = $this->conn->prepare($Query) or die('Unable to Query');
            $q->execute(array(
                ':course_id' => $this->course_id,
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

    //delete course name
    public function delete() {
        if ($this->check()) {
            $qry = "DELETE FROM course WHERE course_id = :course_id";
            $q = $this->conn->prepare($qry) or die("Error");
            $q->execute(array(
                ':course_id' =>$this->course_id,
                ));
            if($q){
                $_SESSION['success_msg'] = "Course has been successfully deleted.";
                header('Location: ../../view/course/index.php');
            }else{
                $_SESSION['error_msg'] = "Course is not deleted. Unexpected error.";
                header('Location: ../../view/course/index.php');
            }
        }else{
            //Course not found
            $_SESSION['error_msg'] = "Sorry! Course is not found.";
            header('Location: ../../view/course/index.php');
        }
    }

}
