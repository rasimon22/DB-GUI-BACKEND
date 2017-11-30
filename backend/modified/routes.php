<?php

use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../classes/ClassVideos.php';
require __DIR__ . '/../classes/ClassUsers.php';
require __DIR__ . '/../classes/ClassUsersLikeVideos.php';

//use \Psr\Http\Message\ServerRequestInterface as Request;
//use \Psr\Http\Message\ResponseInterface as Response;

// Routes

/*$app->get('/', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});*/
//homepage of the video
/*$app->get('/', function (Request $request, Response $response, array $args) {
    if(session_id() == ''){
        session_start(); 
    } 
	//$sql = "SELECT *
          //  from videos t";

        //$stmt = $this->db->query($sql);

        //$results = [];
        //while($row = $stmt->fetch()) {
        //    $results[] = $row;
        //}
//    $videos =  
    // Render index view
//    return $this->view->render($response, 'index.phtml', ["videos" => $videos, "router" => $this->router]);
	return $response;
});

$app->get('/topten', function (Request $request, Response $response, array $args) {
    if(session_id() == ''){
        session_start(); 
    } 
    $classvideos = new ClassVideos($this->db);
    $videos =  $classvideos->getTopTen();
    // Render index view
    return $this->view->render($response, 'index.phtml', ["videos" => $videos, "topten" => "true", "router" => $this->router]);
});

$app->get('/login', function (Request $request, Response $response, array $args) {
	if(session_id() == ''){session_start();}	
	if(  isset($_SESSION['username']) )
	{
		return $response->withRedirect('/');
	}
    $messager = "";
    return $request;
});

$app->get('/logout', function (Request $request, Response $response, array $args) {
	//if(session_id() == ''){session_start();}	
	//	session_destroy();
    return $response->withRedirect("/");
});
*/
$app->post('/login', function (Request $request, Response $response, array $args) {
    $json = $request->getBody();   
    $mydata = json_decode($json,true);    
    $username = $mydata["username"];
    $pass = $mydata["password"];
    $pass = md5($pass);
    //$pass = m$pass);	
    $stmt = $this->db->prepare("SELECT user_id
            from users WHERE username =:user AND password =:pass");
	$stmt->bindParam('user', $username);
	$stmt->bindParam('pass', $pass);
	$stmt->execute();
        $row = $stmt->fetchAll();
	if ($row)
	{
		$getid = $this->db->prepare('SELECT user_id FROM users WHERE username=:user');
		$getid->bindParam('user', $username);
		$getid->execute();
		$check = $getid->fetch(PDO::FETCH_ASSOC);
		$uid = $check['user_id'];
		$jsondata = json_encode(array('user_id' => $uid));
		
		$TOKEN = encodeJWT($jsondata);
		$return = array("Token Created" => true, "TOKEN" => $TOKEN, "user_id" => $uid);
		return $response->withJson($return);
	}  
	else
    return $response->withStatus(401)->withRedirect('/login');
});
/*
$app->get('/addvideos', function (Request $request, Response $response) {    
	if(session_id() == ''){session_start();}	
	if(!isset($_SESSION['username']) )
	{
		return $response->withRedirect('/login');
	}
    return $response;
});

$app->post('/addvideos', function (Request $request, Response $response) {    
	$data = $request->getBody();
	$data = json_decode($data,true);	
	$title = $data["title"];
   	$link = $data["link"];
   	$classvideos = new ClassVideos($this->db);
   	$addvideo =  $classvideos->AddNewVideo($title, $link);
	$JSON = json_encode(array("title" => $title, "link" => $link));
	$response =  $response->withJSON($JSON);
	//$response =  $response->withRedirect("/");
	return $response;
});

/*$app->get('/play/{id}', function (Request $request, Response $response, $args) {
    if(session_id() == ''){session_start();}
    $video_id = (int)$args['id'];    
    $classvideos = new ClassVideos($this->db);
    $videos =  $classvideos->getTopTen();
    $video = $classvideos->getVideoById($video_id);
    $play = $video;
    return $this->view->render($response, 'index.phtml', ["videos" => $videos, "play" => $play,"router" => $this->router]);
});*/
/*
$app->get('/votes/{id}', function (Request $request, Response $response, $args) {    
    if(session_id() == ''){session_start();}
    $video_id = (int)$args['id'];    
    $islike = false;
    $disableBtnLike = "";
    $classlikevideo = new ClassUsersLikeVideos($this->db);
    if(isset($_SESSION['username']))
    {
        $username = $_SESSION['username'];        
        $islike = $classlikevideo->checkUserIsLikeVideo($username, $video_id );    
    }
    $classvideos = new ClassVideos($this->db);
    if(!$islike)
    {
        $video = $classvideos->getVideoById($video_id);
        $votes = 0;
        if(count($video)>0)
            $votes = (int)$video[0]['votes']+1;
        $e = $classvideos->VotesVideo($video_id, $votes);  
        $userlike = $classlikevideo->UserVotesVideo($username, $video_id ); 
    }
    else
    {
        $disableBtnLike = "disabled='disabled'";
    }    
    $videos =  $classvideos->getVideos();    
    $play = $classvideos->getVideoById($video_id); 
    return $this->view->render($response, 'index.phtml', ["disableBtnLike" => $disableBtnLike, "videos" => $videos, "play" => $play,"router" => $this->router]);	
});*/

$app->get('/register', function (Request $request, Response $response, array $args) {
    if(session_id() == ''){session_start();}    
    if(  isset($_SESSION['username']) )
    {
        return $response->withRedirect('/');
    }
    $messager = "";
    return $this->view->render($response, 'register.phtml', ["messager" => $messager, "router" => $this->router]);
});

$app->post('/checkUserExists', function (Request $request, Response $response, array $args) {   
    $json = $request->getBody();   
    $mydata = json_decode($json,true);    
    $username = $mydata["username"];
    $user = new ClassUsers($this->db);
    $check = $user->checkUserExists($username);
    $isexists = "";
    if($check) $isexists = "isexists";
    $data = array('checkExists' => $check , 'username' => $username, 'isexists' => $isexists);   
   return $response->withJson($data,200,
        JSON_UNESCAPED_UNICODE);
});

$app->post('/register', function (Request $request, Response $response, array $args) {
    $json = $request->getBody();   
    $mydata = json_decode($json,true);    
    $username = $mydata["username"];
    $pass = $mydata["password"];
    $user = new ClassUsers($this->db);
    $messager = $user->register($username, $pass);

    $data = array('messager' => $messager , 'username' => $username);   
   return $response->withJson($data,200,
        JSON_UNESCAPED_UNICODE);
});

//for searching video
$app->get('/search-video', function (Request $request, Response $response, $args) {
     if(session_id() == ''){session_start();}  
    return $response;
});

$app->post('/search-video', function (Request $request, Response $response, $args) {
	$data = $request->getParsedBody();	
	$keyword = '';
	$keyword = filter_var($data['keyword'], FILTER_SANITIZE_STRING);
	
	$message = "";
	$videos = '';
	$channels = '';
	$playlists = '';
		
	$DEVELOPER_KEY = 'AIzaSyDyJOLf4HzvRa6yGcgoF_bSa7zK2H4yGjw';

	$client = new Google_Client();
	$client->setDeveloperKey($DEVELOPER_KEY);

	// Define an object that will be used to make all API requests.
	$youtube = new Google_Service_YouTube($client);

	try {

		// Call the search.list method to retrieve results matching the specified
		// query term.
		$searchResponse = $youtube->search->listSearch('id,snippet', array(
		  'q' => $keyword,
		  'maxResults' => 10,
		));

		
        $listVideo = [];
		// Add each result to the appropriate list, and then display the lists of
		// matching videos, channels, and playlists.
        $i = 0;
		foreach ($searchResponse['items'] as $searchResult) {
		  switch ($searchResult['id']['kind']) {
			case 'youtube#video':
			  $videos .= sprintf('<li>%s (%s)</li>',
				  $searchResult['snippet']['title'], $searchResult['id']['videoId']);
                  $listVideo[$i]['title']   =  $searchResult['snippet']['title'];//, 'id':  };
                  $listVideo[$i]['id']      =  $searchResult['id']['videoId'];
                  $i++;
			  break;
			case 'youtube#channel':
			  $channels .= sprintf('<li>%s (%s)</li>',
				  $searchResult['snippet']['title'], $searchResult['id']['channelId']);
                    $listVideo[$i]['title']    =  $searchResult['snippet']['title'];
                    $listVideo[$i]['id']       =  $searchResult['id']['channelId'];
                    $i++;
			  break;
			case 'youtube#playlist':
			  $playlists .= sprintf('<li>%s (%s)</li>',
				  $searchResult['snippet']['title'], $searchResult['id']['playlistId']);
                    $listVideo[$i]['title']       =  $searchResult['snippet']['title'];
                    $listVideo[$i]['id']          =  $searchResult['id']['playlistId'];
                    $i++;
			  break;
		  }
		}

	} catch (Google_Service_Exception $e) {
		$message .= sprintf('<p>A service error occurred: <code>%s</code></p>',
		htmlspecialchars($e->getMessage()));
	} catch (Google_Exception $e) {
		$message .= sprintf('<p>An client error occurred: <code>%s</code></p>',
		htmlspecialchars($e->getMessage()));
	}
	$message = count($listVideo);
	$JSON = json_encode($listVideos);
	$response = $response->withJSON($JSON);
	$response = $response ->withRedirect("/search-video");
	return $response;
});
/*
// test new login
$app->get('/old-login', function (Request $request, Response $response, array $args) {
	if(session_id() == ''){session_start();}	
	if(  isset($_SESSION['username']) )
	{
		return $response->withRedirect('/');
	}
    $messager = "";
    return $this->view->render($response, 'old-login.phtml', ["messager" => $messager, "router" => $this->router]);
});*/

$app->get('/active/{id}', function ( Request $request, Response $response, array $args) {
	$active_id = (int)$args['id'];
	$sql = "  SELECT library.url FROM library INNER JOIN
		active ON library.song_id = active.song_id
		  WHERE active_id =:activeid";
	$stmt = $this->db->prepare($sql);
	$stmt->bindParam('activeid', $active_id);
	$stmt->execute();
	$results = $stmt->fetchAll();
            //$results[] = $row;
        
	//$JSON = json_encode(($results));
	$response = $response->withJSON($results);
	//$response = $response->withRedirect("/active/" + $active_id);
	return $response;
});

$app->post('/active/{id}',  function ( Request $request, Response $response, array $args) {
	$json = $request->getBody();
	$mydata = json_decode($json,true);
        $active_id = (int)$args['id'];
	$user_id = $mydata["user_id"];
	$sql = "SELECT access_code  FROM active INNER JOIN 
		playlists ON active.playlist_id = playlists.playlist_id
		 WHERE active_id = '$active_id'";
	$stmt = $this->db->query($sql);
	$sqlArray =  $stmt->fetch();
	$access_code = implode($sqlArray);
	echo $access_code;
	echo $user_id;	
	$sql = "SELECT access_id FROM access
	WHERE access_code = '$access_code'
	AND user_id = '$user_id';";
	$stmt1 = $this->db->query($sql);
	$sqlArray = $stmt1->fetch();
	echo gettype($sqlArray);
	$intArr = implode( $sqlArray);
	echo $intArr;
	$access_id = implode($sqlArray);
	echo $access_id;
	
	$sql = "SELECT like_id FROM user_likes WHERE user_id = '$user_id' AND access_id = '$access_id' 
		AND active_id = $active_id;";
	$stmt = $this->db->query($sql);
	$results = [];
        while($row = $stmt->fetch()) {
        	$results[] = $row;
        }
	//check if the user already liked the video
	if(count($results)> 0 ? true : false){
	//$response = $response->withRedirect("/active/");
	//might need to return some stuff JSON later
	echo "here2";
	return	$response;	
	}
	echo "gets Here";
	$sql = "UPDATE active SET likes = likes + 1
		 WHERE active_id = '$active_id';";
	$this->db->query($sql);
	$sql = "INSERT INTO user_likes (access_id, user_id, active_id) VALUES 
		('$access_id','$user_id','$active_id');";
	$this->db->query($sql);
	//$response = $response->withRedirect("/active/{id}");
	//might need to return some stuff JSON later*/
	return $response;
});


$app->get('/user/{id}', function( Request $request, Response $response, array $args){
	$user_id = (int) $args['id'];	
	$sql = "SELECT username,fName,lName,email FROM users WHERE user_id = '$user_id'";
	 $stmt = $this->db->query($sql);
        $results = [];
        while($row = $stmt->fetch()) {
                $results[] = $row;
        }
	$json = json_encode($results);
	$response = $response->withJSON($json);
	return $response;
});		

$app->post('/user/{id}', function( Request $request, Response $response, array $args){
	$json = $request->getBody();
	$mydata = json_decode($json,true);
        $user_id = (int) $args['id'];
	$username = $mydata["username"];
	$fName = $mydata["fName"];
	$lName = $mydata["lName"];
	$email = $mydata["email"];
	echo $username;
	echo $fName;
	$sql = "UPDATE users 
		SET username = '$username', fName = '$fName', lName = '$lName', email = '$email'
		WHERE user_id = '$user_id';";
        $this->db->query($sql);
	$response = $response->withJSON($json);
});
	
$app->post('/guest/addplaylist', function (Request $request, Response $response) {
        $data = $request->getBody();
        $data = json_decode($data,true);
        $title = $data["title"];
	$username = $data["username"];
	$access_code = $data["access_code"];
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username=:username");
	$stmt->bindParam('username', $username);
	$stmt->execute();
	$row = $stmt->rowCount();
	if ($row) {
	return $response->withStatus(401);	
}	
	else {

	$stmt = $this->db->prepare("INSERT INTO playlists(title, username, access_code) VALUES (:title, :username, :access_code)");
	$stmt->bindParam('title', $title);
	$stmt->bindParam('username', $username);
	$stmt->bindParam('access_code', $access_code);
	$stmt->execute();

	return $response->withJson($data);
}
        //$addvideo =  $classvideos->AddNewVideo($title, $link);
        //$JSON = json_encode(array("title" => $title, "link" => $link));
        //$response =  $response->withJSON($JSON);
        //$response =  $response->withRedirect("/");
 //       return $response;
});
