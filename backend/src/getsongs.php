<?php

use Slim\Http\Request;
use Slim\Http\Response;


require __DIR__ . '/../vendor/autoload.php';

//require __DIR__ . '/../classes/ClassVideos.php';
//require __DIR__ . '/../classes/ClassUsers.php';
//require __DIR__ . '/../classes/ClassUsersLikeVideos.php';


// get list songs
$app->get('/songs', function (Request $request, Response $response, array $args) {
	$pdo = $this->db;
	$stmt = $pdo->prepare('SELECT l.song_id, l.url, a.active_id,
				a.user_id, a.playlist_id, a.likes
				FROM library as l NATURAL JOIN active as a');
	$stmt->execute();
	$row = $stmt->fetchAll();
	$row['successfully'] = true;
		return $this->response->withJson($row);
});

// Retrieve song with id 
$app->get('/songs/[{id}]', function (Request $request, Response $response, array $args) {
        $pdo = $this->db;
	$sth = $pdo->prepare('SELECT l.song_id, l.url, a.active_id,
                                a.user_id, a.playlist_id, a.likes
                                FROM library as l NATURAL JOIN active as a
				WHERE l.song_id=:id');
        $sth->bindParam("id", $args['id']);
        $sth->execute();
        $row = $sth->fetchAll();
	$row['successfully'] = true;
        return $this->response->withJson($row);
    });



