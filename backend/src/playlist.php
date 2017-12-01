<?php
use Slim\Http\Request;
use Slim\Http\Response;
require __DIR__ . '/../vendor/autoload.php';
//require __DIR__ . '/../classes/ClassVideos.php';
//require __DIR__ . '/../classes/ClassUsers.php';
//require __DIR__ . '/../classes/ClassUsersLikeVideos.php';
// get list playlist
$app->get('/user/playlist/[{uid}]', function (Request $request, Response $response, array $args) {
	$pdo = $this->db;
	$stmt = $pdo->prepare('SELECT p.playlist_id, p.title, p.user_id 
				FROM playlists as p INNER JOIN access as a
				ON p.access_code = a.access_code
				WHERE a.user_id=:uid 
				UNION ALL
				SELECT playlist_id, title, user_id
				FROM playlists WHERE user_id=:uid1');
	$stmt->bindParam('uid', $args['uid']);
	$stmt->bindParam('uid1', $args['uid']);
	$stmt->execute();
	$row = $stmt->fetchAll();
	$row['successfully'] = true;
		return $this->response->withJson($row);
});
// Retrieve playlist with id 
$app->get('/playlist/{id}', function(Request $request, Response $response, array $args)  {
    $sql = "SELECT url, users.username, active.likes, playlists.title 
	    FROM active NATURAL JOIN users NATURAL JOIN library NATUAL JOIN playlists 
	    WHERE (active.playlist_id = :id AND playlists.playlist_id = :id)";
    $query = $this->db->prepare($sql);
    $query->bindParam("id", $args['id']);
    $query->execute();
    $result = $query->fetchAll();
    $result['successfully'] = true;
    return $response->withJSON($result);
});
$app->post('/guest/playlist', function ($request, $response, $args) {
	$json = $request->getBody();
	$data = json_decode($json, true);
	$title = $data['title'];
	$user_id = $data['user_id'];
	//create random string for passcode
	
	function generate() {
	$length = 10;
    	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	
	$charactersLength = strlen($characters);
    	$randomString = '';
    	for ($i = 0; $i < $length; $i++) {
        	$randomString .= $characters[rand(0, $charactersLength - 1)];
    	}
		return $randomString;
	}

	$row = true;
	while ($row) {
	$stmt = $this->db->prepare("SELECT * FROM playlists WHERE access_code=:ac");
	$gen = generate();
        $stmt->bindParam('ac', $gen);
        $stmt->execute();
        $row = $stmt->fetchAll();
        if ($row) {
                generate();
        }
       	}
	$access_code = $gen;
	
	$stmt = $this->db->prepare("INSERT INTO playlists(title, user_id, access_code) VALUES (:title, :userid, :access_code)");
	$stmt->bindParam('title', $title);
	$stmt->bindParam('userid', $user_id);
	$stmt->bindParam('access_code', $access_code);
	$stmt->execute();
	$row = array("successfully" => true);
	return $this->response->withJson($row);
	
});

