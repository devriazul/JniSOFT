<?php

namespace App\Db;

use PDO;
use PDOException;
use App\connection;

class Db {

    public $key, $value, $redirect;
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

        if (!empty($data['key'])) {
            $this->key = $data['key'];
        }

        if (!empty($data['value'])) {
            $this->value = $data['value'];
        } 

        if (!empty($data['redirect'])) {
            $this->redirect = $data['redirect'];
        }else{
            $this->redirect = 'home/index.php';
        }

        return $this;
    }

    // store and update
    public function store() {
        if(!empty($this->key) && !empty($this->value)){
            try {
                $query = "INSERT INTO db (_key, _value) VALUES
                                            (:key, :value )";
                $q = $this->conn->prepare($query);
                $q->execute(array(
                    ':key'    => $this->key,
                    ':value'  => $this->value
                    ));
                $rows = $q->rowCount();
                if($rows>0){
                    $_SESSION['success_msg'] = "Data Successfully Saved";
                    header('Location: ../../view/settings/index.php');
                } else {
                    $_SESSION['error_msg'] = "Data is not Saved.Unexpected error";
                    header('Location: ../../view/settings/index.php');
                }
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }else{
            //empty data
            $_SESSION['error_msg'] = "Store: You can not store empty data.";
            header('Location: ../../view/settings/index.php');
        }
        return $this;
    }

    //Update session info
    public function update(){
        if(!empty($this->key) && !empty($this->value)){
            $qry = "UPDATE db SET _value = :value WHERE _key = :key";
            $q = $this->conn->prepare($qry) or die("Failed");
            $q->execute(array(
                ':value'  => $this->value,
                ':key'  => $this->key
                ));
            if($q){
                $_SESSION['success_msg'] = "Data has been updated successfully";
                header('Location: ../../view/settings/index.php');
            }else{
                $_SESSION['error_msg'] = "Data is not updated.Unexpected error.";
                header('Location: ../../view/settings/index.php');
            }
        }else {
            //empty data
            $_SESSION['error_msg'] = "Update: You can not store empty data.";
            header('Location: ../../view/settings/index.php');
        }
        return $this;
    }

    //check if any value is in database or not
    public function check(){
        try {
            $chkQuery = "SELECT * FROM db WHERE _key = :key";
            $q = $this->conn->prepare($chkQuery) or die("Unable to Query");
            $q->execute(array(
                ':key' => $this->key
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
            $chkQuery = "SELECT * FROM db";
            $q = $this->conn->query($chkQuery);
            $rowCount = $q->rowCount();
            return $rowCount;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $this;
    }

    //get value for key information
    public function get_value(){
        try {
            $Query = "SELECT * FROM db WHERE _key = :key";
            $q = $this->conn->prepare($Query) or die('Unable to Query');
            $q->execute(array(
                ':key' => $this->key
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

   //save value for a key
    public function set_value()
    {
        if (!empty($key) && !empty($value)) {
            if (!$this->check()) {
                $this->assign(array('key' => $this->key, 'value' => $this->value))->store();
            }else{
                $this->update();
            }
        }
    }

    //delete session name
    public function delete() {
        if ($this->check()) {
            $qry = "DELETE FROM db WHERE _key = :key";
            $q = $this->conn->prepare($qry) or die("Error");
            $q->execute(array(
                ':key' =>$this->key
                ));
            if($q){
                $_SESSION['success_msg'] = "Data has been successfully deleted.";
                header('Location: ../../view/settings/index.php');
            }else{
                $_SESSION['error_msg'] = "Data is not deleted. Unexpected error.";
                header('Location: ../../view/settings/index.php');
            }
        }else{
            //session not found
            $_SESSION['error_msg'] = "Sorry! Data is not found.";
            header('Location: ../../view/settings/index.php');
        }
    }

}
