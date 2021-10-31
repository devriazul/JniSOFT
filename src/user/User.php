<?php
namespace App\User;
// require_once(__DIR__.'/../connection/Connection.php');

use PDO;
use PDOException;
use App\connection;

class User {

    public $user_id, $user_name, $email, $password, $user_type;
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

        if (!empty($data['user_id'])) {
            $this->user_id = $data['user_id'];
        }
        if (!empty($data['user_name'])) {
            $this->user_name = $data['user_name'];
        }
        if (!empty($data['email'])) {
            $this->email = $data['email'];
        }
        if (!empty($data['password'])) {
            $this->password = $data['password'];
        }
        if (!empty($data['user_type'])) {
            $this->user_type = $data['user_type'];
        }

        return $this;
    }

    // add session name in `session` table
    public function store() {
        if(!empty($this->user_id) && !empty($this->user_name) && !empty($this->email) && !empty($this->password) && !empty($this->user_type) ){
            try {
                $query = "INSERT INTO user (user_id, user_name, email, password, user_type, created_at, updated_at, deleted_at) VALUES
                                            (:user_id, :user_name, :email, :password, :user_type, now(), :updated_at, :deleted_at )";
                $q = $this->conn->prepare($query);
                $q->execute(array(
                    ':user_id'    => $this->user_id,
                    ':user_name'  => $this->user_name,
                    ':email'  => $this->email,
                    ':password'  => $this->password,
                    ':user_type'  => $this->user_type,
                    ':updated_at'   => null,
                    ':deleted_at'   => null,
                    ));
                $rows = $q->rowCount();
                if($rows>0){
                    $_SESSION['success_msg'] = "User Successfully Added";
                    header('Location: ../../view/user/user-add.php');
                } else {
                    $_SESSION['error_msg'] = "User is not added.Unexpected error";
                    header('Location: ../../view/user/user-add.php');
                }
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }else{
            //empty data
            $_SESSION['error_msg'] = "Please enter all the required information";
            header('Location: ../../view/user/user-add.php');
        }
        return $this;
    }

    //Update user info
    public function update(){
        if( !empty($this->user_id) && !empty($this->user_name) ) {
            $qry = "UPDATE user SET user_name = :user_name, updated_at = now() WHERE user_id = :user_id";
            $q = $this->conn->prepare($qry) or die("Failed");
            $q->execute(array(
                ':user_id'      => $this->user_id,
                ':user_name'    => $this->user_name,
               // ':email'        => $this->email,
                ));
            if($q){
                $_SESSION['success_msg'] = "User data has been updated successfully";
                header('Location: ../../view/settings/index.php');
            }else{
                $_SESSION['error_msg'] = "Session is not updated. Unexpected error.";
                header('Location: ../../view/settings/index.php');
            }
        }else {
            //empty data
            $_SESSION['error_msg'] = "Please enter all the required information";
            header('Location: ../../view/settings/index.php');
        }
        return $this;
    }

    //update password
        //Update session info
    public function update_password(){
        if( !empty($this->password) && !empty($this->user_id) ) {
            $qry = "UPDATE user SET password = :password, updated_at = now() WHERE user_id = :user_id";
            $q = $this->conn->prepare($qry) or die("Failed");
            $q->execute(array(
                ':password'     => $this->password,
                ':user_id'      => $this->user_id,
                ));
            if($q){
                $_SESSION['success_msg'] = "Password has been updated successfully";
                header('Location: ../../view/settings/index.php');
            }else{
                $_SESSION['error_msg'] = "Password is not updated. Unexpected error.";
                header('Location: ../../view/settings/index.php');
            }
        }else {
            //empty data
            $_SESSION['error_msg'] = "Please enter all the required information";
            header('Location: ../../view/settings/index.php');
        }
        return $this;
    }

    // get all session data
    public function all($limit, $offset){    
       try {
            $Query = "SELECT * FROM user ORDER BY created_at ASC LIMIT $limit OFFSET $offset";
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
    public function all_by_type($limit, $offset){    
       try {
            $Query = "SELECT * FROM user WHERE user_type = :user_type ORDER BY created_at ASC LIMIT $limit OFFSET $offset";
            $q = $this->conn->prepare($Query) or die("Unable to Query");
            $q->execute(array(
                ':user_type' => $this->user_type,
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

    //check if any user is in database or not
    public function check(){
        try {
            $chkQuery = "SELECT * FROM user WHERE user_id = :user_id";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':user_id' => $this->user_id,
                ));
            $rowCount = $q->rowCount();
            return $rowCount;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $this;
    }

    //check if any email is in database or not
    public function check_email(){
        try {
            $chkQuery = "SELECT * FROM user WHERE email = :email";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':email' => $this->email,
                ));
            $rowCount = $q->rowCount();
            return $rowCount;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $this;
    }

    //check if any user is in database or not
    public function login(){
        try {
            $chkQuery = "SELECT * FROM user WHERE email = :email AND password = :password";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':email' => $this->email,
                ':password' => $this->password,
                ));
            $rowCount = $q->rowCount();
            return $rowCount;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $this;
    }

    //get total user number for pagination
    public function total(){
        try {
            $chkQuery = "SELECT * FROM user";
            $q = $this->conn->query($chkQuery);
            $rowCount = $q->rowCount();
            return $rowCount;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $this;
    }

    //get total session number for pagination
    public function total_user_by_type(){
        try {
            $chkQuery = "SELECT * FROM user WHERE user_type = :user_type";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':user_type' => $this->user_type,
                ));
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
            $Query = "SELECT * FROM user WHERE user_id = :user_id";
            $q = $this->conn->prepare($Query) or die('Unable to Query');
            $q->execute(array(
                ':user_id' => $this->user_id,
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

    //get Single user information via email
    public function single_by_email(){
        try {
            $Query = "SELECT * FROM user WHERE email = :email";
            $q = $this->conn->prepare($Query) or die('Unable to Query');
            $q->execute(array(
                ':email' => $this->email
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
            $qry = "DELETE FROM user WHERE user_id = :user_id";
            $q = $this->conn->prepare($qry) or die("Error");
            $q->execute(array(
                ':user_id' =>$this->user_id,
                ));
            if($q){
                $_SESSION['success_msg'] = "User has been successfully deleted.";
                header('Location: ../../view/user/index.php');
            }else{
                $_SESSION['error_msg'] = "User is not deleted. Unexpected error.";
                header('Location: ../../view/user/index.php');
            }
        }else{
            //session not found
            $_SESSION['error_msg'] = "Sorry! User is not found.";
            header('Location: ../../view/user/index.php');
        }
    }

}
