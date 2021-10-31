<?php

namespace App\Marks;

use PDO;
use PDOException;
use App\connection;

class Marks {

    public $session, $semester, $course_id, $result, $student_id;
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

        if (!empty($data['session'])) {
            $this->session = $data['session'];
        }
        if (!empty($data['semester'])) {
            $this->semester = $data['semester'];
        }
        if (!empty($data['course_id'])) {
            $this->course_id = $data['course_id'];
        }
        if (!empty($data['result'])) {
            $this->result = $data['result'];
        }
        if (!empty($data['student_id'])) {
            $this->student_id = $data['student_id'];
        }
        return $this;
    }

    // save marks
    public function store() {
        if(!empty($this->session) && !empty($this->semester) && !empty($this->course_id) && !empty($this->result)  ){
            try {
                $query = "INSERT INTO marks (session, semester, course_id, result, created_at, updated_at, deleted_at) VALUES
                                            (:session, :semester, :course_id, :result, now(), :updated_at, :deleted_at )";
                $q = $this->conn->prepare($query);
                $q->execute(array(
                    ':session'       => $this->session,
                    ':semester'        => $this->semester,
                    ':course_id'     => $this->course_id,
                    ':result'            => $this->result,
                    ':updated_at'       => null,
                    ':deleted_at'       => null,
                    ));
                $rows = $q->rowCount();
                $e = $q->errorInfo();
                $_SESSION['error_msg'] = $e[2];
                if($rows>0){
                    $_SESSION['success_msg'] = "Grade Successfully Added";
                    header('Location: ../../view/marks/marks-add.php?semester='.$this->semester.'&session='.$this->session.'&course_id='.$this->course_id);
                } else {
                    $_SESSION['error_msg'] = "Grade is not added.Unexpected error";
                    header('Location: ../../view/marks/marks-add.php?semester='.$this->semester.'&session='.$this->session.'&course_id='.$this->course_id);
                }
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }else{
            //empty data
            $_SESSION['error_msg'] = "Please enter all the required information";
            header('Location: ../../view/marks/marks-add.php?semester='.$this->semester.'&session='.$this->session.'&course_id='.$this->course_id);
        }
        return $this;
    }

    //Update session info
    //if redirect is zero then it redirect to mark-add page
    //if not redirect to marks-edit
    public function update($redirect = 0){
        if(!empty($this->session) && !empty($this->semester) && !empty($this->course_id) && !empty($this->result) ){
            $qry = "UPDATE marks SET result = :result,  updated_at = now() WHERE session = :session AND semester = :semester AND course_id = :course_id";
            $q = $this->conn->prepare($qry) or die("Failed");
            $q->execute(array(
                ':result'       => $this->result,
                ':session'      => $this->session,
                ':semester'     => $this->semester,
                ':course_id'    => $this->course_id
                ));
            if($q){
                if ($redirect == 0) {
                    $_SESSION['success_msg'] = "Grade has been updated successfully";
                    header('Location: ../../view/marks/marks-add.php?semester='.$this->semester.'&session='.$this->session.'&course_id='.$this->course_id);
                }else{
                    $_SESSION['success_msg'] = "Grade has been updated successfully";
                    header('Location: ../../view/marks/marks-edit.php?semester='.$this->semester.'&session='.$this->session.'&course_id='.$this->course_id);
                }
            }else{
                if ($redirect == 0) {
                    $_SESSION['error_msg'] = "Grade is not updated.Unexpected error.";
                    header('Location: ../../view/marks/marks-add.php?semester='.$this->semester.'&session='.$this->session.'&course_id='.$this->course_id);
                }else{
                    $_SESSION['error_msg'] = "Grade is not updated.Unexpected error.";
                    header('Location: ../../view/marks/marks-edit.php?semester='.$this->semester.'&session='.$this->session.'&course_id='.$this->course_id);
                }
            }
        }else {
            //empty data
            if ($redirect == 0) {
                $_SESSION['error_msg'] = "Please enter all the required information";
                header('Location: ../../view/marks/marks-add.php?semester='.$this->semester.'&session='.$this->session.'&course_id='.$this->course_id);
            }else{
                $_SESSION['error_msg'] = "Please enter all the required information";
                header('Location: ../../view/marks/marks-edit.php?semester='.$this->semester.'&session='.$this->session.'&course_id='.$this->course_id);
            }
        }
        return $this;
    }

    // get all session data
    public function all(){    
       try {
            $qry = "SELECT * FROM marks WHERE session = :session AND semester = :semester";
            $q = $this->conn->prepare($qry) or die("Failed");
            $q->execute(array(
                ':session'      => $this->session,
                ':semester'     => $this->semester,
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

    //search marks by roll
    public function all_by_session_semester(){    
       try {
            $Query = "SELECT course.course_name,course.credit,course.course_code,marks.* FROM course,marks WHERE course.course_id = marks.course_id AND marks.session = :session AND marks.semester = :semester";
            //$q = $this->conn->query($Query);
             $q = $this->conn->prepare($Query) or die("Failed");
             $q->execute(array(
                 ':session'     => $this->session,
                 ':semester'    => $this->semester
                 )
             );

            $rowCount = $q->rowCount();

            if ($rowCount > 0) {
                while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
                    $this->data[] = $row;
                }
                return $this->data;
            } else {
                return array();
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $this;
    }

    //search marks by sessionsemester course id
    public function all_by_session_semester_course_id(){    
       try {
            $Query = "SELECT course.course_name,course.credit,marks.* FROM course,marks WHERE course.course_id = marks.course_id AND marks.session = :session AND marks.semester = :semester AND course.course_id = :course_id";
            //$q = $this->conn->query($Query);
             $q = $this->conn->prepare($Query) or die("Failed");
             $q->execute(array(
                 ':session'     => $this->session,
                 ':semester'    => $this->semester,
                 ':course_id'   => $this->course_id
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

    /*
    *   promote student
    *
    */
    public function promote_student()
    {   
      //result map
      $grade_map = array(
        'A' => 4, 
        'B' => 3, 
        'C' => 2, 
        'D' => 1, 
        'F' => 0 
        );

      //echo 'Session: '.$this->session .'<br>';
      //echo 'semester: '.$this->semester .'<br>';

        $Query1 = "SELECT * FROM course WHERE session = :session AND semester = :semester";
             $q1 = $this->conn->prepare($Query1) or die("Failed");
             $q1->execute(array(
                 ':session'     => $this->session,
                 ':semester'    => $this->semester
                 )
             );

            $course_num = $q1->rowCount();
            //echo 'course number for this semester: '. $course_num . '<br>';

        $Query = "SELECT * FROM marks WHERE session = :session AND semester = :semester";
         $q = $this->conn->prepare($Query) or die("Failed");
         $q->execute(array(
             ':session'     => $this->session,
             ':semester'    => $this->semester
             )
         );

        $marks_num = $q->rowCount();
        //echo 'In marks table course: '. $marks_num . '<br>';

        //check all marks for this session,semester is published
        if ($course_num == $marks_num) {
            //all result is published for this semester
            $Query = "SELECT course.course_id, course.credit,marks.* FROM course,marks WHERE course.session = :session AND course.semester = :semester AND marks.session = :session AND marks.semester = :semester AND course.course_id = marks.course_id";
             $q = $this->conn->prepare($Query) or die("Failed");
             $q->execute(array(
                 ':session'     => $this->session,
                 ':semester'    => $this->semester
                 )
             );

            $rowCount = $q->rowCount();
            
            if ($rowCount > 0) {
                while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
                    $this->data[] = $row;
                }
            }else{
                $this->data = array();
            }

            //results = $this->all_by_session_semester();
            $results = $this->data;

            //if has any result
            if (is_array($results) && !empty($results)) {
                $total_point = 0;
                $total_credit = 0;
                $all_pass = true;
                foreach ($results as $result) {
                    $my_result = unserialize($result['result']);

                    //grade
                    if (array_key_exists($this->student_id, $my_result) && !empty($my_result[$this->student_id]) ) {
                        $grade_value =  $my_result[$this->student_id];

                        if ($grade_value == 'F') {
                            $all_pass = false;
                        }
                    }else{
                        $grade_value = 'N/A';
                        $all_pass = false;
                    }

                    //point
                    if (array_key_exists($this->student_id, $my_result) && !empty($my_result[$this->student_id]) ) {
                        $point_value =  $result['credit']*$grade_map[$my_result[$this->student_id]];
                        $total_point += $point_value;
                        $total_credit += $result['credit'];
                    }else{
                        $point_value = 'N/A';
                    }
                } //end foreach

                //check point here
                //echo 'Total point: '. round($total_point/$total_credit, 2);


                //check all pass by grade here
                if ($all_pass) {
                    $semester = '';
                    //echo 'All passed';
                    //update semester to next one
                    if($this->semester == '1-1') {
                        $semester = '1-2';
                    }else if($this->semester == '1-2') {
                        $semester = '2-1';
                    }else if($this->semester == '2-1') {
                        $semester = '2-2';
                    }else if($this->semester == '2-2') {
                        $semester = '3-1';
                    }else if($this->semester == '3-1') {
                        $semester = '3-2';
                    }

                    if (!empty($semester)) {
                        //update this semester to student table
                        if(!empty($this->student_id) && !empty($semester) ){
                            $qry = "UPDATE student SET semester = :semester, updated_at = now() WHERE student_id = :student_id";
                            $q = $this->conn->prepare($qry) or die("Failed");
                            $q->execute(array(
                                ':semester' => $semester,
                                ':student_id'   => $this->student_id
                                ));
                            if($q){
                                //yeaa 
                                //echo 'Promoted';
                            }else{
                               // echo 'Not promoted';
                            }
                        }
                        //echo 'Next semester: '.$semester;
                    }
                }else{
                    //echo 'Not all passed';
                }
            }
        }
    }

    //check if a result is in database or not for a course
    public function check(){
        try {
            $chkQuery = "SELECT * FROM marks WHERE course_id = :course_id AND session = :session AND semester = :semester";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':course_id'    => $this->course_id,
                ':session'      => $this->session,
                ':semester'     => $this->semester
                ));
            $rowCount = $q->rowCount();
            return $rowCount;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $this;
    }

    //check if a result is in database or not
    public function check_all(){
        try {
            $chkQuery = "SELECT * FROM marks WHERE session = :session AND semester = :semester";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':session'      => $this->session,
                ':semester'     => $this->semester
                ));
            $rowCount = $q->rowCount();
            return $rowCount;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $this;
    }

    //search marks by roll
    public function search(){    
       try {
            $Query = "SELECT course.course_name,course.credit,marks.* FROM course,marks WHERE course.course_id = marks.course_id";
            $q = $this->conn->query($Query);
            // $q = $this->conn->prepare($Query) or die("Failed");
            // $q->execute(array(
            //     ':session'   => $this->session,
            //     ':semester'     => $this->semester
            //     )
            // );

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

    //get total session number for pagination
    public function total(){
        try {
            $chkQuery = "SELECT * FROM session";
            $q = $this->conn->query($chkQuery);
            $rowCount = $q->rowCount();
            return $rowCount;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $this;
    }

    //get Single result information
    public function single(){
        try {
            $Query = "SELECT * FROM marks WHERE session = :session AND semester = :semester  AND course_id = :course_id";
            $q = $this->conn->prepare($Query) or die('Unable to Query');
            $q->execute(array(
                ':course_id'   => $this->course_id,
                ':semester'    => $this->semester,
                ':session'      => $this->session,
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

    //delete session name
    public function delete() {
        if ($this->check()) {
            $qry = "DELETE FROM marks WHERE student_id = :student_id AND course_id = :course_id AND session = :session AND semester = :semester";
            $q = $this->conn->prepare($qry) or die("Error");
            $q->execute(array(
                ':student_id'       => $this->student_id,
                ':course_id'        => $this->course_id,
                ':session'          => $this->session,
                ':semester'         => $this->semester
                ));
            if($q){
                $_SESSION['success_msg'] = "Grade has been successfully deleted.";
                header('Location: ../../view/marks/marks-add.php?semester='.$this->semester.'&session='.$this->session.'&course_id='.$this->course_id);
            }else{
                $_SESSION['error_msg'] = "Grade is not deleted. Unexpected error.";
                header('Location: ../../view/marks/marks-add.php?semester='.$this->semester.'&session='.$this->session.'&course_id='.$this->course_id);
            }
        }else{
            //session not found
            $_SESSION['error_msg'] = "Sorry!Grade is not found.";
            header('Location: ../../view/marks/marks-add.php?semester='.$this->semester.'&session='.$this->session.'&course_id='.$this->course_id);
        }
    }

}
