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
    return $response->withStatus(200);
});

$app->get('/logout', function (Request $request, Response $response, array $args) {
    return $response->withRedirect('/'); 
});

$app->get('/register', function (Request $request, Response $response, array $args) {
    #if(session_id() == ''){session_start();} 
    return $response->withStatus(200);
});

$app->post('/register', function (Request $request, Response $response, array $args) {
    $json = $request->getBody();   
    $userData = json_decode($json,true);    
    $username = $userData["username"];
    $pass = $userData["password"];
    $fName = $userData["firstName"];
    $lName = $userData["lastName"];
    $email = $userData["email"];
    $user = new ClassUsers($this->db);
	if($username == "" || $pass == "" || $fName == "" || $lName == "" || $email == ""){
                $false = array('success' => false , 'error' => 'blank input');
                $response = $response->withJSON(json_encode($false));
                //$response = $response->withRedirect('/register');
                return $response;
       }
	$pass = md5($pass);
	$sql = "SELECT count(*)
            from users WHERE username = '$username'";
        $stmt = $this->db->query($sql);
        $results = $stmt->fetch();
        if($results['count(*)'] > 0){
                if(session_id() == ''){session_start();}
                $false = array('success' => false , 'error' => 'username taken');
                $response = $response->withJSON(json_encode($false));
                //$response = $response->withRedirect('/register');
                return $response;
        }
    $returnData = $user->register($username, $pass, $fName, $lName, $email);
	$sql = $sql = "SELECT user_id
            from users WHERE username = '$username' AND password = '$pass'";
	 $stmt = $this->db->query($sql);
        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }
        $myJSON = json_encode(array($results));
	$response = $response->withJSON($myJSON);
	return $response;
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
            $returnData = array("valid"=>true, "userID" => $user);
            return $response->withJson($returnData,200, JSON_UNESCAPED_UNICODE);
        }
        else{
            return $response->withStatus(418);  
        }
    }
    else{
        return $response->withRedirect('/changePassword');
    }
});

$app->get('/playlist/{id}', function(Request $request, Response $response, array $args)  {
    $sql = "SELECT url,
users.username,
active.likes,
playlists.title
from active NATURAL JOIN users NATURAL JOIN library NATUAL JOIN playlists WHERE (active.playlist_id = :id AND playlists.playlist_id = :id)";
    $query = $this->db->prepare($sql);
    $query->bindParam("id", $args['id']);
    $query->execute();
    $result = $query->fetchAll();
    return $response->withJSON($result);
});



