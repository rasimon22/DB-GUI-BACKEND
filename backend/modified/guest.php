<?php

use Slim\Http\Request;
use Slim\Http\Response;


require __DIR__ . '/../vendor/autoload.php';

//require __DIR__ . '/../classes/ClassVideos.php';
//require __DIR__ . '/../classes/ClassUsers.php';
//require __DIR__ . '/../classes/ClassUsersLikeVideos.php';


// get list songs
$app->post('/guest', function (Request $request, Response $response, array $args) {
	$json = $request->getBody();
	$data = json_decode($json, true);
	$username = $data['username'];	
	$isGuest = 1;
	$check = $this->db->prepare("SELECT * FROM users WHERE username=:usercheck");
	$check->bindParam('usercheck', $username);
	$check->execute();
	$isExist = $check->rowCount();
	if ($isExist) {
		//return $this->response->withStatus(401)->withHeader('Location', '/guest');
		return $response->withStatus(401);
	}
	else
	{
	$stmt = $this->db->prepare("INSERT INTO users(username, guestBool) VALUES (:user, :bool)");
	$stmt->bindParam('user', $username);
	$stmt->bindParam('bool', $isGuest);
	$stmt->execute();
	$stmt1 = $this->db->prepare("SELECT user_id FROM users WHERE username=:user1");
		$getid->bindParam('user1', $username);
		$getid->execute();
		$check = $getid->fetch(PDO::FETCH_ASSOC);
		$uid = $check['user_id'];
		$jsondata = json_encode(array('user_id' => $uid));
		
		$TOKEN = encodeJWT($jsondata);
		$return = array("Token Created" => true, "TOKEN" => $TOKEN, "user_id" => $uid);
		return $response->withJson($return);
	}
});


