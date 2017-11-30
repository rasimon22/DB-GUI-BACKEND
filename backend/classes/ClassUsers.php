<?php

class ClassUsers 
{
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function checkLogin($user,$pass) {
        $pass = md5($pass); 
        $sql = "SELECT username, password 
            from users WHERE username = '$user' AND password = '$pass'";
            
        $stmt = $this->db->query($sql);

        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }
        return count($results)> 0 ? true : false;
    }

    public function checkUserExists($user) {        
        $sql = "SELECT *
            from users WHERE username = '$user'";
            
        $stmt = $this->db->query($sql);

       $results = [];
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }
        return count($results)> 0 ? true : false;
    }

    public function register($user,$pass, $firstName, $lastName, $email) { 

        $sql = "insert into users
            (username, password, fName, lName, email) values
            (:user, :pass, :fName, :lName, :email)";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            "user" => $user,
            "pass" => $pass,
	    "fName" => $firstName,
	    "lName" => $lastName,
	    "email" => $email
        ]);

        if($result) {
            return array( "valid" => true,
			  "userName" => $user);
			

        }
	else{
        return array("valid" => false, "userName" => "null"); 
	}
    }
    
}
