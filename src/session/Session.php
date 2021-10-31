<?php

namespace App\Session;

use PDO;
use PDOException;
use App\connection;

class Session {

    public $session_id, $session_name;
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

        if (!empty($data['session_id'])) {
            $this->session_id = $data['session_id'];
        }

        if (!empty($data['session_name'])) {
            $this->session_name = $data['session_name'];
        }

        return $this;
    }

    // add session name in `session` table
    public function store() {
        if(!empty($this->session_id) && !empty($this->session_name)){
            try {
                $query = "INSERT INTO session (session_id, session_name, created_at, updated_at, deleted_at) VALUES
                                            (:session_id, :session_name, now(), :updated_at, :deleted_at )";
                $q = $this->conn->prepare($query);
                $q->execute(array(
                    ':session_id'    => $this->session_id,
                    ':session_name'  => $this->session_name,
                    ':updated_at'   => null,
                    ':deleted_at'   => null,
                    ));
                $rows = $q->rowCount();
                if($rows>0){
                    $_SESSION['success_msg'] = "Session Successfully Added";
                    header('Location: ../../view/session/session-add.php');
                } else {
                    $_SESSION['error_msg'] = "Session is not added.Unexpected error";
                    header('Location: ../../view/session/session-add.php');
                }
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }else{
            //empty data
            $_SESSION['error_msg'] = "Please enter all the required information";
            header('Location: ../../view/session/session-add.php');
        }
        return $this;
    }

    //Update session info
    public function update(){
        if(!empty($this->session_name) && !empty($this->session_id)){
            $qry = "UPDATE session SET session_name = :session_name, updated_at = now() WHERE session_id = :session_id";
            $q = $this->conn->prepare($qry) or die("Failed");
            $q->execute(array(
                ':session_name'  => $this->session_name,
                ':session_id'  => $this->session_id

                ));
            if($q){
                $_SESSION['success_msg'] = "Session data has been updated successfully";
                header('Location: ../../view/session/session-edit.php?session='.$this->session_id);
            }else{
                $_SESSION['error_msg'] = "Session is not updated. Unexpected error.";
                header('Location: ../../view/session/session-edit.php?session='.$this->session_id);
            }
        }else {
            //empty data
            $_SESSION['error_msg'] = "Please enter all the required information";
            header('Location: ../../view/session/session-edit.php?session='.$this->session_id);
        }
        return $this;
    }

    // get all session data
    public function all($limit, $offset){    
       try {
            $Query = "SELECT * FROM session ORDER BY session_name DESC LIMIT $limit OFFSET $offset";
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

    // get all session data
    public function all_full(){    
       try {
            $Query = "SELECT * FROM session ORDER BY session_name DESC";
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

    //check if any session is in database or not
    public function check(){
        try {
            $chkQuery = "SELECT * FROM session WHERE session_id = :session_id";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':session_id' => $this->session_id,
                ));
            $rowCount = $q->rowCount();
            return $rowCount;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $this;
    }

        //check if any session is in database or not
    public function check_name(){
        try {
            $chkQuery = "SELECT * FROM session WHERE session_name = :session_name";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':session_name' => $this->session_name,
                ));
            $rowCount = $q->rowCount();
            return $rowCount;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
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

    //get Single session information
    public function single(){
        try {
            $Query = "SELECT * FROM session WHERE session_id = :session_id";
            $q = $this->conn->prepare($Query) or die('Unable to Query');
            $q->execute(array(
                ':session_id' => $this->session_id,
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
            $qry = "DELETE FROM session WHERE session_id = :session_id";
            $q = $this->conn->prepare($qry) or die("Error");
            $q->execute(array(
                ':session_id' =>$this->session_id,
                ));
            if($q){
                $_SESSION['success_msg'] = "Session has been successfully deleted.";
                header('Location: ../../view/session/index.php');
            }else{
                $_SESSION['error_msg'] = "Session is not deleted. Unexpected error.";
                header('Location: ../../view/session/index.php');
            }
        }else{
            //session not found
            $_SESSION['error_msg'] = "Sorry! Session is not found.";
            header('Location: ../../view/session/index.php');
        }
    }

}
