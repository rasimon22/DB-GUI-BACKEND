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
		return $this->response->withJson($row);
});
// Retrieve playlist with id 
$app->get('/playlist/[{id}]', function ($request, $response, $args) {
        $pdo = $this->db;
	$sth = $pdo->prepare('SELECT p.playlist_id, p.title, p.user_id,
                                access_code, 
                                FROM playlists as p INNER JOIN active as a
                                ON p.playlist_id = a.playlist_id
                                INNER JOIN library as l
                                ON a.song_id = l.song_id WHERE p.playlist_id=:id');
        $sth->bindParam("id", $args['id']);
        $sth->execute();
        $row = $sth->fetchObject();
        return $this->response->withJson($row);
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
	$public = $data['isPublic'];
	
	$stmt = $this->db->prepare("INSERT INTO playlists(title, user_id, access_code, isPublic) VALUES (:title, :userid, :access_code, :public)");
	$stmt->bindParam('title', $title);
	$stmt->bindParam('userid', $user_id);
	$stmt->bindParam('access_code', $access_code);
	$stmt->bindParam('public', $public);
	$stmt->execute();
	return $response->withStatus(200);
	
});

