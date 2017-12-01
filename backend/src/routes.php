<?php
use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../classes/ClassVideos.php';
require __DIR__ . '/../classes/ClassUsers.php';
require __DIR__ . '/../classes/ClassUsersLikeVideos.php';

// Routes
//homepage of the video
$app->get('/', function (Request $request, Response $response, array $args) {
    $res = array("successfully"=>true);
    return $response->withJSON(json_encode($res));
});

$app->get('/logout', function (Request $request, Response $response, array $args) {
    return $response->withRedirect('/'); 
});

$app->get('/register', function (Request $request, Response $response, array $args) {
    $res = array("successfully"=>true);
    return $response->withJSON(json_encode($res));
});

$app->post('/register', function (Request $request, Response $response, array $args) {
    $json = $request->getBody();   
    $userData = json_decode($json,true);    
    $username = $userData["username"];
    $pass = $userData["password"];
    $fName = $userData["username"];
    $lName = $userData["username"];
    $email = $userData["email"];
    $user = new ClassUsers($this->db);
	if($username == "" || $pass == "" || $fName == "" || $lName == "" || $email == ""){
                $false = array('successfully' => false , 'error' => 'blank input');
                $response = $response->withJSON(json_encode($false))->withStatus(401);
                //$response = $response->withRedirect('/register');
                return $response;
       }
	$pass = md5($pass);
	$sql = "SELECT count(*)
            from users WHERE username = '$username'";
        $stmt = $this->db->query($sql);
        $results = $stmt->fetch();
        if($results['count(*)'] > 0){
                $false = array('successfully' => false , 'error' => 'username taken');
                $response = $response->withJSON(json_encode($false))->withStatus(401);
                //$response = $response->withRedirect('/register');
                return $response;
        }
    $returnData = $user->register($username, $pass, $fName, $lName, $email);
	$sql = "SELECT user_id
            from users WHERE username = '$username' AND password = '$pass'";
	$stmt = $this->db->query($sql);
        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = $row;
        } 
        $results["successfully"]=true;
        $myJSON = json_encode(array($results));
	$response = $response->withJSON($myJSON);
	return $response;
});
   
$app->get('/changePassword', function(Request $request, Response $response, array $args) {
    $result = array("successfully"=> true);
    return $response->withJSON(json_encode($result));
});

$app->put('/changePassword', function(Request $request, Response $response, array $args){
//TODO: fix error handling from status 405 to status 418
    $json = $request->getBody();   
    $userData = json_decode($json,true);    
    $user = $userData["username"];
    $pass = $userData["password"];
    $newPass = $userData["newPassword"];
    $userObj = new ClassUsers($this->db);
    if($userObj->checkLogin($user,$pass)){
        $pass = md5($newPass);
        $sql = "UPDATE users SET password = '$pass' WHERE username = '$user'"; 
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(); 
        if($result){
            $returnData = array("userName" => $user, "successfully"=>true,"newPassword" =>$pass);
            return $response->withJson($returnData,200, JSON_UNESCAPED_UNICODE);
        }
        else{
            $res = array("successfully" => false, "error"=>"database error");
            return $response->withStatus(401)->withJSON($res);
        }
    }
    else{
        $res = array("sucessfully"=>false, "error"=>"invalid credentials");
        return $response->withJSON($res)->withStatus(401);
    }
});





